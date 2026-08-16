/*
	cube.ui.js — ⋯ 드롭다운 여러 개 동시 열림 방지 + 맨 위로 버튼.
	드롭다운 자체는 <details class="cube-dropdown"> 네이티브 요소라 열고 닫는 것 자체는 JS 없이도 된다.
	여기서는 "다른 걸 열면 이전 것 닫기" 같은 다듬기만 한다.
*/
(function (window, $) {
	'use strict';

	$(function () {
		// ── 드롭다운: 하나 열리면 나머지는 닫는다 ──
		document.addEventListener('toggle', function (e) {
			var el = e.target;
			if (!(el instanceof HTMLDetailsElement) || !el.classList.contains('cube-dropdown') || !el.open) {
				return;
			}
			document.querySelectorAll('.cube-dropdown[open]').forEach(function (other) {
				if (other !== el) {
					other.open = false;
				}
			});
		}, true);

		// 바깥을 클릭하면 열린 드롭다운을 닫는다.
		$(document).on('click', function (e) {
			var $target = $(e.target);
			if ($target.closest('.cube-dropdown').length) {
				return;
			}
			$('.cube-dropdown[open]').prop('open', false);
		});

		// ── 맨 위로 버튼 ──
		var $top = $('[data-role="top"]');
		if ($top.length) {
			var toggleTop = function () {
				if (window.scrollY > 600) {
					$top.removeAttr('hidden');
				} else {
					$top.attr('hidden', true);
				}
			};
			toggleTop();
			$(window).on('scroll', toggleTop);
			$top.on('click', function () {
				var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
				window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
			});
		}
	});
})(window, jQuery);
