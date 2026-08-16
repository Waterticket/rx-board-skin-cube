{{-- 출력 없음. $list_config(게시판 관리 > 목록 설정)을 카드 슬롯 맵으로 변환한다. 02_라이믹스_적용_명세.md §5 참조. --}}
@php
	$cube_cols = [];
	$cube_extra = [];
	foreach (($list_config ?? []) as $cube_col_key => $cube_col_val) {
		if ((int)$cube_col_val->idx === -1) {
			$cube_cols[$cube_col_val->type] = true;
		} else {
			$cube_extra[] = $cube_col_val;
		}
	}
	// title은 카드가 성립하지 않으므로 목록 설정에서 빠져 있어도 강제로 켠다.
	$cube_cols['title'] = true;
@endphp
