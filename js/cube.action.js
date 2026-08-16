/*
	cube.action.js — 추천 · 스크랩 · 신고 · 링크복사. document/comment 공용.
	AJAX는 커스텀 fetch가 아니라 코어의 exec_json() 을 쓴다 (common/js/xml_handler.js:205).
	CSRF 헤더 자동 첨부 + 실패 시 토큰 재발급 후 자동 재시도가 이미 구현되어 있다.
	모든 target_srl 기반 액션(document.controller.php:27, comment.controller.php:20 등)은
	document_srl/comment_srl 이 아니라 target_srl 하나로 통일되어 있다.
*/
(function (window, $) {
	'use strict';

	$(function () {
		var Cube = window.Cube || {};

		// ── 문서 추천 (상세 페이지 전용 — 목록에서는 getMyVote() 를 부르지 않는다) ──
		$(document).on('click', '.cube-vote-btn', function () {
			if (!Cube.requireLogin()) {
				return;
			}
			var $btn = $(this);
			if ($btn.data('busy')) {
				return;
			}
			var srl = $btn.data('srl');
			var voted = $btn.attr('aria-pressed') === 'true';
			var act = voted ? 'procDocumentVoteUpCancel' : 'procDocumentVoteUp';

			$btn.data('busy', true);
			exec_json('document.' + act, { target_srl: srl }, function (res) {
				$btn.data('busy', false);
				if (!res || res.error) {
					return;
				}
				var count = (typeof res.voted_count !== 'undefined') ? res.voted_count : null;
				$btn.attr('aria-pressed', voted ? 'false' : 'true');
				$btn.toggleClass('is-active', !voted);
				if (count !== null) {
					$btn.find('[data-role="vote-count"]').text(count);
				}
			}, function () {
				$btn.data('busy', false);
			});
		});

		// ── 댓글 추천 ──
		$(document).on('click', '.cube-comment-vote', function () {
			if (!Cube.requireLogin()) {
				return;
			}
			var $btn = $(this);
			if ($btn.data('busy')) {
				return;
			}
			var srl = $btn.data('srl');
			var voted = $btn.attr('aria-pressed') === 'true';
			var act = voted ? 'procCommentVoteUpCancel' : 'procCommentVoteUp';

			$btn.data('busy', true);
			exec_json('comment.' + act, { target_srl: srl }, function (res) {
				$btn.data('busy', false);
				if (!res || res.error) {
					return;
				}
				var count = (typeof res.voted_count !== 'undefined') ? res.voted_count : null;
				$btn.attr('aria-pressed', voted ? 'false' : 'true');
				$btn.toggleClass('is-active', !voted);
				if (count !== null) {
					$btn.find('[data-role="vote-count"]').text(count);
				}
			}, function () {
				$btn.data('busy', false);
			});
		});

		// ── 스크랩. 목록 카드는 초기 스크랩 여부를 서버에서 미리 알려주지 않는다
		//    (문서당 추가 쿼리를 피하기 위해 — 02문서 §8.4). 그래서 클릭 시점의 클래스만으로 토글한다. ──
		$(document).on('click', '.cube-scrap', function () {
			if (!Cube.requireLogin()) {
				return;
			}
			var $btn = $(this);
			if ($btn.data('busy')) {
				return;
			}
			var srl = $btn.data('srl');
			var scrapped = $btn.attr('aria-pressed') === 'true';
			var act = scrapped ? 'procMemberDeleteScrap' : 'procMemberScrapDocument';

			$btn.data('busy', true);
			exec_json('member.' + act, { target_srl: srl }, function (res) {
				$btn.data('busy', false);
				if (!res || res.error) {
					return;
				}
				$btn.attr('aria-pressed', scrapped ? 'false' : 'true');
				$btn.toggleClass('is-active', !scrapped);
			}, function () {
				$btn.data('busy', false);
			});
		});

		// ── 신고. declare_message 를 물어보는 최소 구현. ──
		$(document).on('click', '.cube-report-btn, .cube-comment-report-btn', function () {
			if (!Cube.requireLogin()) {
				return;
			}
			var $btn = $(this);
			var isComment = $btn.hasClass('cube-comment-report-btn');
			var srl = $btn.data('srl');
			var message = window.prompt('신고 사유를 입력해 주세요.');
			if (message === null) {
				return;
			}
			var action = isComment ? 'comment.procCommentDeclare' : 'document.procDocumentDeclare';
			exec_json(action, {
				target_srl: srl,
				declare_message: message
			}, function (res) {
				if (!res || res.error) {
					return;
				}
				window.alert('신고가 접수되었습니다.');
			});
		});

		// ── 링크 복사 ──
		$(document).on('click', '.cube-copy-btn', function () {
			var $btn = $(this);
			var url = $btn.data('url') || location.href;
			var done = function () {
				var $label = $btn.find('[data-role="copy-label"]');
				var original = $label.text();
				$label.text('복사됨');
				setTimeout(function () {
					$label.text(original);
				}, 1500);
			};
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(url).then(done, function () {
					window.prompt('아래 링크를 복사하세요', url);
				});
			} else {
				window.prompt('아래 링크를 복사하세요', url);
			}
		});

		// ── 새 댓글 작성 · 답글. board.procBoardInsertComment 는 board 모듈 액션이라 mid가 필요하다
		//    (document/comment/member 액션과 달리 이것만 mid로 게시판 컨텍스트를 찾는다). ──
		function refreshComments() {
			var url = location.href.split('#')[0];
			fetch(url + (url.indexOf('?') > -1 ? '&' : '?') + 'layout=none', {
				credentials: 'same-origin',
				headers: { 'X-Requested-With': 'XMLHttpRequest' }
			})
				.then(function (res) {
					return res.text();
				})
				.then(function (html) {
					var doc = new DOMParser().parseFromString(html, 'text/html');
					var fresh = doc.getElementById('comment');
					var $current = $('#comment');
					if (fresh && $current.length) {
						$current.replaceWith(fresh);
					}
				});
		}

		// 스티커 등록(js/cube.sticker.js)도 같은 경로로 댓글을 남긴다 — 등록 액션과 새로고침 방식을 한 곳에만 둔다.
		Cube.refreshComments = refreshComments;

		// $form 안의 textarea[name=content] (+ 있으면 nick_name/password)를 읽어 댓글을 등록하고
		// 성공하면 댓글 영역 전체를 새로고침한다. 새 댓글 폼과 인라인 답글 폼이 공유한다.
		function submitCommentForm($form, extraParams) {
			var $msg = $form.find('[data-role="form-msg"]');
			var $btn = $form.find('button[type="submit"]');
			var content = $form.find('textarea[name="content"]').val();
			if (!content || !content.trim()) {
				if ($msg.length) {
					$msg.text('내용을 입력해 주세요.');
				}
				return;
			}

			var params = $.extend({
				mid: Cube.mid,
				content: content
			}, extraParams);
			var $nick = $form.find('input[name="nick_name"]');
			var $pw = $form.find('input[name="password"]');
			if ($nick.length) {
				params.nick_name = $nick.val();
			}
			if ($pw.length) {
				params.password = $pw.val();
			}

			$btn.prop('disabled', true);
			if ($msg.length) {
				$msg.text('등록 중…');
			}

			exec_json('board.procBoardInsertComment', params, function (res) {
				$btn.prop('disabled', false);
				if ($msg.length) {
					$msg.text('');
				}
				if (!res || res.error) {
					return;
				}
				refreshComments();
			}, function () {
				$btn.prop('disabled', false);
				if ($msg.length) {
					$msg.text('');
				}
			});
		}

		$(document).on('submit', '#cube-comment-form', function (e) {
			e.preventDefault();
			var $form = $(this);
			submitCommentForm($form, { document_srl: $form.data('document-srl') });
		});

		// ── 답글: 새 페이지로 이동하지 않고 클릭한 댓글 바로 아래에 폼을 연다 ──
		function buildReplyFormHtml() {
			var guestFields = Cube.isLogged ? '' :
				'<div class="cube-comment-guest">' +
				'<input type="text" name="nick_name" class="cube-comment-guest-input" placeholder="닉네임" required />' +
				'<input type="password" name="password" class="cube-comment-guest-input" placeholder="비밀번호" required />' +
				'</div>';
			// 스티커 버튼/패널의 마크업은 comment/form.blade.php 와 같은 구조여야 한다 — cube.sticker.js 가 둘을 구분하지 않는다.
			var stickerBtn = Cube.useSticker ?
				'<button type="button" class="cube-sticker-btn" data-role="sticker-toggle" aria-expanded="false">' + Cube.icon.smile + '스티커</button>' : '';
			var stickerPanel = Cube.useSticker ? '<div class="cube-sticker" data-role="sticker-panel" hidden></div>' : '';
			return (
				'<form class="cube-comment-form cube-reply-form">' +
				'<textarea name="content" class="cube-comment-input" placeholder="답글을 남겨주세요." rows="2" required></textarea>' +
				guestFields +
				'<div class="cube-comment-form-foot">' +
				stickerBtn +
				'<span class="cube-comment-form-msg" data-role="form-msg" aria-live="polite"></span>' +
				'<button type="button" class="cube-btn" data-role="cancel-reply">취소</button>' +
				'<button type="submit" class="cube-btn cube-btn-primary">등록</button>' +
				'</div>' +
				stickerPanel +
				'</form>'
			);
		}

		$(document).on('click', '.cube-comment-reply-btn', function () {
			// 새 댓글 폼과 마찬가지로 로그인 여부를 여기서 막지 않는다 — 비회원 댓글 허용 여부는
			// 서버(procBoardInsertComment)가 판단하고, 이 버튼 자체는 write_comment 권한이 있을 때만 렌더된다.
			var $btn = $(this);
			var srl = $btn.data('srl');
			var $slot = $('.cube-reply-form-slot[data-comment-srl="' + srl + '"]');
			if (!$slot.length) {
				return;
			}

			var isOpen = $slot.children().length > 0;
			$('.cube-reply-form-slot').empty();
			$('.cube-comment-reply-btn').attr('aria-expanded', 'false');

			if (isOpen) {
				return;
			}

			$slot.html(buildReplyFormHtml());
			$btn.attr('aria-expanded', 'true');
			$slot.find('textarea').trigger('focus');
		});

		$(document).on('click', '[data-role="cancel-reply"]', function () {
			$(this).closest('.cube-reply-form-slot').empty();
			$('.cube-comment-reply-btn').attr('aria-expanded', 'false');
		});

		$(document).on('submit', '.cube-reply-form', function (e) {
			e.preventDefault();
			var $form = $(this);
			var $slot = $form.closest('.cube-reply-form-slot');
			var documentSrl = $('#comment').data('document-srl');
			submitCommentForm($form, {
				document_srl: documentSrl,
				parent_srl: $slot.data('comment-srl')
			});
		});
	});
})(window, jQuery);
