/*
	cube.list.js — 더 보기 / 무한 스크롤.
	서버는 paging_mode 와 무관하게 항상 페이지네이션을 렌더한다(list/pager.blade.php). 여기서는 그 위에
	paging_mode 가 'more' 또는 'infinite' 일 때만 대체 UI를 켜고 fetch로 다음 페이지를 이어붙인다.
	getUrl('page', N) 이 카테고리·검색어·정렬을 이미 보존해 주므로 이 파일은 URL을 직접 조립하지 않는다.
*/
(function (window, $) {
	'use strict';

	$(function () {
		var Cube = window.Cube || {};
		var $root = $('.cube').first();
		if (!$root.length) {
			return;
		}

		var mode = $root.attr('data-paging');
		var $list = $('#cube-list');
		var $loadmoreBtn = $('[data-role="loadmore"]');
		var $sentinel = $('[data-role="sentinel"]');
		var $loadend = $('[data-role="loadend"]');
		var $announce = $('[data-role="announce"]');
		var $pagerWrap = $('[data-js-hide="pager"]');

		if (mode === 'paging' || !$list.length || !$loadmoreBtn.length) {
			return;
		}

		$pagerWrap.attr('hidden', true);

		var isLoading = false;

		/*
			읽던 위치 복원.
			주소에 page 번호를 적어 두는 방식은 쓸 수 없다 — 서버는 page=3 이면 3페이지'만' 주므로
			1~3을 이어 붙인 화면과 달라지고, 짧은주소(/board/page/2)에서는 애초에 page 파라미터도 없다.
			그래서 '몇 페이지까지 이어 붙였는지 + 스크롤 위치'를 sessionStorage 에 적어 두고,
			글을 보고 목록으로 돌아온 경우에만 그만큼 다시 이어 붙인 뒤 스크롤을 되돌린다.
			처음 들어온 사람에게 갑자기 아래로 튀지 않도록 복원 조건을 좁게 잡는 게 핵심이다.
		*/
		var RESTORE_MAX_PAGES = 20;
		var storeKey = 'cube:list:' + location.pathname + location.search;
		var loadedPages = 1;

		function saveState() {
			try {
				if (loadedPages <= 1) {
					window.sessionStorage.removeItem(storeKey);
					return;
				}
				window.sessionStorage.setItem(storeKey, JSON.stringify({
					pages: loadedPages,
					y: window.pageYOffset || document.documentElement.scrollTop || 0
				}));
			} catch (e) {
				// 시크릿 모드 등에서 sessionStorage 가 막혀 있으면 복원 기능만 조용히 포기한다.
			}
		}

		// 뒤로가기이거나, 이 목록 아래 경로(=글 상세 /board/3212)에서 넘어온 경우에만 복원한다.
		function cameBackFromArticle() {
			var nav = (window.performance && window.performance.getEntriesByType) ?
				window.performance.getEntriesByType('navigation')[0] : null;
			if (nav && nav.type === 'back_forward') {
				return true;
			}
			if (!document.referrer) {
				return false;
			}
			try {
				var ref = new URL(document.referrer);
				var base = location.pathname.replace(/\/$/, '') + '/';
				return ref.host === location.host && ref.pathname.indexOf(base) === 0;
			} catch (e) {
				return false;
			}
		}

		function restoreState() {
			var state = null;
			try {
				state = JSON.parse(window.sessionStorage.getItem(storeKey) || 'null');
			} catch (e) {
				return;
			}
			if (!state || !(state.pages > 1) || !cameBackFromArticle()) {
				return;
			}

			var target = Math.min(state.pages, RESTORE_MAX_PAGES);
			if ('scrollRestoration' in window.history) {
				window.history.scrollRestoration = 'manual';
			}

			// prev 와 loadedPages 가 같으면 직전 요청이 실패한 것이다 — 무한 재시도를 막는다.
			(function step(prev) {
				if (loadedPages >= target || loadedPages === prev || !$list.attr('data-next-url')) {
					// behavior:'auto' 를 명시한다. 레이아웃이 html 에 scroll-behavior:smooth 를 걸어 두면
					// 수천 px 를 애니메이션으로 기어가고, 그 사이 사용자가 휠을 굴리면 도중에 취소된다.
					window.requestAnimationFrame(function () {
						window.scrollTo({ top: state.y || 0, left: 0, behavior: 'auto' });
						if ('scrollRestoration' in window.history) {
							window.history.scrollRestoration = 'auto';
						}
					});
					return;
				}
				var current = loadedPages;
				loadNext().then(function () {
					step(current);
				});
			})(0);
		}

		function finish(hasMore) {
			isLoading = false;
			$loadmoreBtn.text('더 보기');
			if (!hasMore) {
				$loadmoreBtn.attr('hidden', true);
				if (observer && $sentinel.length) {
					observer.unobserve($sentinel[0]);
				}
				$loadend.removeAttr('hidden');
			}
		}

		/*
			다음 페이지를 layout=none 으로 받아오면, 그 응답 안의 링크는 전부 현재 요청 인자를 물려받아
			layout=none&page=N 을 달고 나온다(getUrl 이 현재 요청 인자를 보존하기 때문). 그대로 이어 붙이면
			이어 붙인 카드의 글 링크를 눌렀을 때 레이아웃 없는 알몸 페이지가 뜬다.
			page 도 같이 턴다 — 어디까지 봤는지는 sessionStorage 가 기억하므로 주소에 남길 이유가 없다.
		*/
		// dropPage 는 글 링크에만 준다. 다음 페이지 주소에서 page 를 털면 페이징 자체가 멈춘다
		// (짧은주소가 꺼져 있으면 다음 주소가 ?mid=board&page=2 형태로 온다).
		function stripFetchParams(url, dropPage) {
			try {
				var u = new URL(url, location.href);
				var changed = false;
				if (u.searchParams.has('layout')) {
					u.searchParams.delete('layout');
					changed = true;
				}
				if (dropPage && u.searchParams.has('page')) {
					u.searchParams.delete('page');
					changed = true;
				}
				return changed ? (u.pathname + u.search + u.hash) : url;
			} catch (e) {
				return url;
			}
		}

		function cleanLinks(root) {
			var links = root.querySelectorAll('a[href]');
			for (var i = 0; i < links.length; i++) {
				var href = links[i].getAttribute('href');
				if (href && href.charAt(0) !== '#') {
					links[i].setAttribute('href', stripFetchParams(href, true));
				}
			}
		}

		// 복원 루틴이 '한 페이지 더 붙는 것'을 기다려야 해서 Promise 를 돌려준다.
		function loadNext() {
			var nextUrl = $list.attr('data-next-url');
			if (!nextUrl || isLoading) {
				return Promise.resolve();
			}
			isLoading = true;
			$loadmoreBtn.text('불러오는 중…');

			return fetch(nextUrl + (nextUrl.indexOf('?') > -1 ? '&' : '?') + 'layout=none', {
				credentials: 'same-origin',
				headers: { 'X-Requested-With': 'XMLHttpRequest' }
			})
				.then(function (res) {
					if (!res.ok) {
						throw new Error('http ' + res.status);
					}
					return res.text();
				})
				.then(function (html) {
					var doc = new DOMParser().parseFromString(html, 'text/html');
					var nextListEl = doc.getElementById('cube-list');
					var cards = nextListEl ? nextListEl.querySelectorAll('.cube-card') : [];
					var frag = document.createDocumentFragment();
					for (var i = 0; i < cards.length; i++) {
						frag.appendChild(cards[i]);
					}
					cleanLinks(frag);
					$list[0].appendChild(frag);

					var newNextUrl = nextListEl ? nextListEl.getAttribute('data-next-url') : '';
					$list.attr('data-next-url', newNextUrl ? stripFetchParams(newNextUrl, false) : '');
					loadedPages++;

					if (cards.length && $announce.length) {
						$announce.text(cards.length + '개 글을 더 불러왔습니다.');
					}

					finish(!!newNextUrl);
				})
				.catch(function () {
					isLoading = false;
					$loadmoreBtn.text('더 보기').removeAttr('hidden');
				});
		}

		var observer = null;

		if (mode === 'infinite' && window.IntersectionObserver) {
			$loadmoreBtn.attr('hidden', true);
			$sentinel.removeAttr('hidden');
			observer = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						loadNext();
					}
				});
			}, { rootMargin: '400px 0px' });
			if ($sentinel.length) {
				observer.observe($sentinel[0]);
			}
		} else {
			// 'more' 모드이거나 IntersectionObserver 미지원 브라우저는 버튼으로 폴백한다.
			$loadmoreBtn.removeAttr('hidden');
			$loadmoreBtn.on('click', loadNext);
		}

		// pagehide 는 뒤로가기 캐시로 나갈 때도 불린다(unload 와 달리 bfcache 를 깨지 않는다).
		$(window).on('pagehide', saveState);
		restoreState();
	});
})(window, jQuery);
