/*
	cube.core.js — 네임스페이스 + 공용 헬퍼.
	jQuery / exec_json / getCSRFToken 은 common/js/common.js, xml_handler.js 가 항상 먼저 로드되어 있다
	(classes/display/HTMLDisplayHandler.php 가 스킨과 무관하게 core JS를 주입한다). 여기서 다시 로드하지 않는다.
*/
(function (window, $) {
	'use strict';

	var Cube = window.Cube || {};

	Cube.$root = null;
	Cube.isLogged = false;
	Cube.mid = '';
	Cube.useSticker = false;
	Cube.stickerStoreUrl = '';
	Cube.stickerMylistUrl = '';

	Cube.init = function () {
		Cube.$root = $('.cube').first();
		if (!Cube.$root.length) {
			return;
		}
		Cube.isLogged = Cube.$root.attr('data-logged') === '1';
		Cube.mid = Cube.$root.attr('data-mid') || '';
		Cube.useSticker = Cube.$root.attr('data-sticker') === '1';
		Cube.stickerStoreUrl = Cube.$root.attr('data-sticker-store') || '';
		Cube.stickerMylistUrl = Cube.$root.attr('data-sticker-mylist') || '';
	};

	/*
		JS가 그려 넣는 마크업에서 쓰는 아이콘. lib/icons.blade.php 의 같은 이름 항목과 모양을 맞춰 둔다
		— 서버가 그린 버튼(comment/form.blade.php)과 JS가 그린 버튼(답글 폼)이 나란히 보이기 때문이다.
	*/
	Cube.icon = {
		smile: '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" aria-hidden="true"><circle cx="8" cy="8" r="6.3"/><path d="M5.4 9.4a3 3 0 0 0 5.2 0"/><path d="M6 6.2v.01M10 6.2v.01" stroke-width="1.8"/></svg>',
		close: '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path d="M3.5 3.5l9 9M12.5 3.5l-9 9"/></svg>',
		chevronUp: '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 10 4-4 4 4"/></svg>',
		chevronDown: '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 6 4 4 4-4"/></svg>',
		reorder: '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5V3.5M1.8 5.7 4 3.5l2.2 2.2M12 3.5v9M9.8 10.3 12 12.5l2.2-2.2"/></svg>',
		shop: '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.6 5.4h10.8l-.9 7.2a1 1 0 0 1-1 .9H4.5a1 1 0 0 1-1-.9z"/><path d="M5.8 5.4V4a2.2 2.2 0 0 1 4.4 0v1.4"/></svg>'
	};

	// sticker 모듈이 돌려주는 파일 경로는 './files/attach/...' 형태의 상대경로다.
	// 짧은주소(/mid/document_srl)에서는 현재 경로 기준으로 풀려 엉뚱한 URL이 되므로 항상 default_url 기준으로 세운다.
	Cube.absUrl = function (url) {
		if (!url) {
			return '';
		}
		if (/^(?:https?:)?\/\//.test(url) || url.charAt(0) === '/') {
			return url;
		}
		return default_url + String(url).replace(/^\.\//, '');
	};

	// 비로그인 상태에서 로그인 필요한 동작을 눌렀을 때 공용으로 쓴다.
	Cube.requireLogin = function () {
		if (Cube.isLogged) {
			return true;
		}
		if (window.confirm('로그인이 필요합니다. 로그인 페이지로 이동할까요?')) {
			location.href = request_uri + (request_uri.indexOf('?') > -1 ? '&' : '?') + 'act=dispMemberLoginForm';
		}
		return false;
	};

	Cube.escapeHtml = function (str) {
		return String(str == null ? '' : str).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	};

	window.Cube = Cube;

	$(function () {
		Cube.init();
	});
})(window, jQuery);
