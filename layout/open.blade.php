{{-- 스킨 루트. data-* 속성으로 CSS/JS 분기하고, --cube-* 변수는 인라인 style로 skin.xml 설정값을 덮어쓴다. --}}
<div class="cube"
	data-mid="{{ $mid }}"
	data-columns="{{ $cube['columns'] }}"
	data-paging="{{ $cube['pagingMode'] }}"
	data-logged="{{ $is_logged ? '1' : '0' }}"
	data-sticker="{{ $cube['useSticker'] ? '1' : '0' }}"
	data-sticker-store="{{ $cube['stickerStoreUrl'] }}"
	data-sticker-mylist="{{ $cube['stickerMylistUrl'] }}"
	style="--cube-max:{{ (int)$cube['maxWidth'] }}px;--cube-thumb:{{ (int)$cube['thumbSize'] }}px;"
>
