/*
	cube.sticker.js — 댓글 스티커 선택기.

	동작은 sticker 모듈 API 두 개로 끝난다 (modules/board/skins/sketchbook5/js/sticker.js 와 같은 흐름):
	  sticker.getCommentStickerList {page}        -> {sticker:[{sticker_srl,title,main_image}]}   내가 가진 팩 목록(페이지 단위)
	  sticker.getStickerElemList    {sticker_srl} -> {stickerImage:[{sticker_file_srl,name,url}]} 그 팩의 스티커들

	팩 목록이 페이지 단위인 것은 모듈 쪽 사정이다 — 한 번에 PC 12개 / 모바일 5개만 준다
	(modules/sticker/sticker.model.php:44). 그래서 arca 처럼 전체를 한 줄로 이어 붙이지 않고
	팩 목록 위아래에 페이지 이동 버튼을 둔다.

	등록은 sketchbook5 처럼 XML-RPC를 직접 만들지 않고 cube 가 이미 쓰는 경로를 그대로 쓴다.
	본문이 "{@sticker:팩번호|파일번호}" 한 줄이면 sticker 모듈의 comment.insertComment 트리거가
	가로채 검증·치환한다 (modules/sticker/sticker.controller.php:131).
*/
(function (window, $) {
	'use strict';

	// 스티커 연속 등록 간격. sketchbook5 의 stickerConfig.delayTime 과 같은 값(ms).
	var POST_INTERVAL = 3000;

	$(function () {
		var Cube = window.Cube || {};
		if (!Cube.useSticker) {
			return;
		}

		var packCache = {};
		var blockedUntil = 0;

		// 스티커는 mp4(움짤)로 저장되는 경우가 있다. 같은 이름의 .webp 가 포스터로 함께 올라간다
		// (modules/sticker/sticker.controller.php:270 이 상세 출력에서 쓰는 규칙과 동일).
		function mediaHtml(url, size) {
			var src = Cube.absUrl(url);
			var attr = 'width="' + size + '" height="' + size + '" loading="lazy"';
			if (/\.mp4$/i.test(src)) {
				return '<video ' + attr + ' poster="' + Cube.escapeHtml(src.replace(/\.mp4$/i, '.webp')) +
					'" preload="metadata" autoplay loop muted playsinline><source src="' + Cube.escapeHtml(src) + '" type="video/mp4"></video>';
			}
			return '<img src="' + Cube.escapeHtml(src) + '" ' + attr + ' alt="" />';
		}

		/*
			하단 링크는 새 탭으로 연다 — 작성 중이던 댓글 입력값을 날리지 않기 위해서다.
			순서 조정(dispStickerMylist)은 비로그인이면 서버가 막으므로 URL 자체가 비어 있고,
			그때는 버튼을 아예 렌더하지 않는다 (lib/config.blade.php).
		*/
		function footHtml() {
			var html = '';
			if (Cube.stickerMylistUrl) {
				html += '<a class="cube-sticker-link" href="' + Cube.stickerMylistUrl + '" target="_blank" rel="noopener">' +
					Cube.icon.reorder + '순서 조정</a>';
			}
			if (Cube.stickerStoreUrl) {
				html += '<a class="cube-sticker-link" href="' + Cube.stickerStoreUrl + '" target="_blank" rel="noopener">' +
					Cube.icon.shop + '스티커</a>';
			}
			return html ? '<div class="cube-sticker-foot">' + html + '</div>' : '';
		}

		function shellHtml() {
			return (
				'<div class="cube-sticker-head">' +
				'<span class="cube-sticker-title">스티커</span>' +
				'<button type="button" class="cube-sticker-close" data-role="sticker-close" aria-label="닫기">' + Cube.icon.close + '</button>' +
				'</div>' +
				'<div class="cube-sticker-main">' +
				'<div class="cube-sticker-packs" data-role="sticker-packs"></div>' +
				'<div class="cube-sticker-body" data-role="sticker-body"></div>' +
				'</div>' +
				footHtml()
			);
		}

		function message($panel, text) {
			$panel.find('[data-role="sticker-body"]').html('<p class="cube-sticker-msg">' + Cube.escapeHtml(text) + '</p>');
		}

		// 팩 목록 그리기. page 는 1부터. 목록이 비면(=가진 스티커가 없으면) 안내만 남긴다.
		function loadPacks($panel, page) {
			page = page || 1;
			exec_json('sticker.getCommentStickerList', { page: page }, function (res) {
				var packs = (res && res.sticker) || [];
				if (!packs.length) {
					if (page > 1) {
						return loadPacks($panel, page - 1);
					}
					$panel.find('[data-role="sticker-packs"]').empty();
					message($panel, Cube.isLogged ? '사용할 수 있는 스티커가 없습니다.' : '스티커는 로그인 후 사용할 수 있습니다.');
					return;
				}

				var html = '<button type="button" class="cube-sticker-page" data-role="sticker-page" data-page="' + (page - 1) + '"' +
					(page <= 1 ? ' disabled' : '') + ' aria-label="이전 스티커">' + Cube.icon.chevronUp + '</button>';
				$.each(packs, function (i, pack) {
					html += '<button type="button" class="cube-sticker-pack" data-role="sticker-pack" data-srl="' + parseInt(pack.sticker_srl, 10) +
						'" title="' + Cube.escapeHtml(pack.title) + '">' + mediaHtml(pack.main_image, 44) + '</button>';
				});
				html += '<button type="button" class="cube-sticker-page" data-role="sticker-page" data-page="' + (page + 1) + '" aria-label="다음 스티커">' +
					Cube.icon.chevronDown + '</button>';

				$panel.find('[data-role="sticker-packs"]').html(html).attr('data-page', page);
				$panel.find('[data-role="sticker-pack"]').eq(0).trigger('click');
			}, function () {
				message($panel, '스티커 목록을 불러오지 못했습니다.');
			});
		}

		function renderPack($panel, srl, packName, images) {
			var html = '<span class="cube-sticker-pack-title">' + Cube.escapeHtml(packName) + '</span><div class="cube-sticker-grid">';
			$.each(images, function (i, image) {
				html += '<button type="button" class="cube-sticker-item" data-role="sticker-pick" data-srl="' + srl +
					'" data-file-srl="' + parseInt(image.sticker_file_srl, 10) + '" title="' + Cube.escapeHtml(image.name) + '">' +
					mediaHtml(image.url, 90) + '</button>';
			});
			$panel.find('[data-role="sticker-body"]').html(html + '</div>').scrollTop(0);
		}

		$(document).on('click', '[data-role="sticker-toggle"]', function () {
			var $btn = $(this);
			var $panel = $btn.closest('form').find('[data-role="sticker-panel"]');
			if (!$panel.length) {
				return;
			}
			if (!$panel.prop('hidden')) {
				$panel.prop('hidden', true);
				$btn.attr('aria-expanded', 'false');
				return;
			}

			// 폼이 여러 개(새 댓글 + 답글) 열려 있을 수 있다. 한 번에 하나만 펼친다.
			$('[data-role="sticker-panel"]').prop('hidden', true);
			$('[data-role="sticker-toggle"]').attr('aria-expanded', 'false');

			$panel.prop('hidden', false);
			$btn.attr('aria-expanded', 'true');
			if (!$panel.children().length) {
				$panel.html(shellHtml());
				loadPacks($panel, 1);
			}
		});

		$(document).on('click', '[data-role="sticker-close"]', function () {
			var $form = $(this).closest('form');
			$form.find('[data-role="sticker-panel"]').prop('hidden', true);
			$form.find('[data-role="sticker-toggle"]').attr('aria-expanded', 'false');
		});

		$(document).on('click', '[data-role="sticker-page"]', function () {
			var $btn = $(this);
			var page = parseInt($btn.data('page'), 10);
			if (page < 1) {
				return;
			}
			loadPacks($btn.closest('[data-role="sticker-panel"]'), page);
		});

		$(document).on('click', '[data-role="sticker-pack"]', function () {
			var $btn = $(this);
			var $panel = $btn.closest('[data-role="sticker-panel"]');
			var srl = parseInt($btn.data('srl'), 10);
			var name = $btn.attr('title') || '';

			$panel.find('[data-role="sticker-pack"]').removeClass('is-active');
			$btn.addClass('is-active');

			if (packCache[srl]) {
				renderPack($panel, srl, name, packCache[srl]);
				return;
			}
			message($panel, '불러오는 중…');
			exec_json('sticker.getStickerElemList', { sticker_srl: srl }, function (res) {
				var images = (res && res.stickerImage) || [];
				if (!images.length) {
					message($panel, '이 스티커에는 사용할 수 있는 이미지가 없습니다.');
					return;
				}
				packCache[srl] = images;
				renderPack($panel, srl, name, images);
			}, function () {
				message($panel, '스티커를 불러오지 못했습니다.');
			});
		});

		// 스티커 댓글은 본문이 스티커 코드 한 줄이다. 텍스트와 섞어 보내면 트리거가 코드만 남기고
		// 나머지를 버리므로(sticker.controller.php:169) 아예 입력창 내용을 건드리지 않는다.
		// 로그인 여부로 여기서 막지 않는다 — 비회원 댓글 허용 여부와 기본 스티커 사용 권한은
		// 서버(procBoardInsertComment + sticker 트리거)가 판단한다. cube.action.js 의 댓글 폼과 같은 원칙.
		$(document).on('click', '[data-role="sticker-pick"]', function () {
			var now = Date.now();
			if (now < blockedUntil) {
				window.alert('스티커는 ' + (POST_INTERVAL / 1000) + '초에 한 번만 등록할 수 있습니다.');
				return;
			}

			var $btn = $(this);
			var $form = $btn.closest('form');
			var $panel = $btn.closest('[data-role="sticker-panel"]');
			var $slot = $form.closest('.cube-reply-form-slot');
			var $msg = $form.find('[data-role="form-msg"]');
			var params = {
				mid: Cube.mid,
				document_srl: $('#comment').data('document-srl') || $form.data('document-srl'),
				content: '{@sticker:' + parseInt($btn.data('srl'), 10) + '|' + parseInt($btn.data('file-srl'), 10) + '}'
			};
			if ($slot.length) {
				params.parent_srl = $slot.data('comment-srl');
			}
			var $nick = $form.find('input[name="nick_name"]');
			var $pw = $form.find('input[name="password"]');
			if ($nick.length) {
				params.nick_name = $nick.val();
			}
			if ($pw.length) {
				params.password = $pw.val();
			}

			blockedUntil = now + POST_INTERVAL;
			$panel.addClass('is-busy');
			if ($msg.length) {
				$msg.text('등록 중…');
			}

			exec_json('board.procBoardInsertComment', params, function (res) {
				$panel.removeClass('is-busy');
				if ($msg.length) {
					$msg.text('');
				}
				if (!res || res.error) {
					return;
				}
				$panel.prop('hidden', true);
				$form.find('[data-role="sticker-toggle"]').attr('aria-expanded', 'false');
				Cube.refreshComments();
			}, function () {
				$panel.removeClass('is-busy');
				if ($msg.length) {
					$msg.text('');
				}
				// 등록에 실패했으면 간격 제한도 풀어 준다.
				blockedUntil = 0;
			});
		});
	});
})(window, jQuery);
