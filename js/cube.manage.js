/*
	cube.manage.js — 목록 전체 선택.

	코어에도 XE.checkboxToggleAll({doClick:true}) 가 있지만 쓰지 않는다. 그 함수는 name=cart 체크박스를
	전부 click() 해서 상태를 뒤집는 '반전'이지(common/js/common.js:686-690) '전체 선택'이 아니다.
	이미 체크된 글이 있으면 그것들이 오히려 풀린다.

	세션에 담는 액션(document.procDocumentAddCart)도 토글이므로, 원하는 상태와 다른 체크박스만
	click() 하면 화면과 세션이 한 번에 맞는다. click() 이면 카드에 걸린 인라인 onclick(doAddDocumentCart)이
	그대로 타므로 담기 요청은 코어가 알아서 묶어 보낸다.
*/
(function (window, $) {
	'use strict';

	$(function () {
		var $all = $('[data-role="cart-checkall"]');
		var $list = $('#cube-list');
		if (!$all.length || !$list.length) {
			return;
		}

		function sync() {
			var $boxes = $list.find('input[name="cart"]');
			var total = $boxes.length;
			var checked = $boxes.filter(':checked').length;
			$all.prop('disabled', total === 0);
			$all.prop('checked', total > 0 && checked === total);
			$all.prop('indeterminate', checked > 0 && checked < total);
		}

		$all.on('change', function () {
			var want = $(this).prop('checked');
			$list.find('input[name="cart"]').each(function () {
				if (this.checked !== want) {
					this.click();
				}
			});
			sync();
		});

		$(document).on('change', '#cube-list input[name="cart"]', sync);

		// 무한 스크롤/더 보기로 카드가 붙으면 전체 선택 상태가 어긋난다(cube.list.js 가 .cube-card 를 append 한다).
		if (window.MutationObserver) {
			new window.MutationObserver(sync).observe($list[0], { childList: true });
		}

		sync();
	});
})(window, jQuery);
