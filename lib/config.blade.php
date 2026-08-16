{{-- 출력 없음. skin.xml extra_vars를 $cube 배열로 정규화한다. list.blade.php / comment.html / category.html 이 lib/boot 를 통해 1회만 include 한다. --}}
@php
	$cube = [
		'showBoardTitle'   => ($module_info->show_board_title ?? 'Y') !== 'N',
		'boardDescription' => trim((string)($module_info->board_description ?? '')),
		'showSearch'       => ($module_info->show_search ?? 'N') === 'Y',
		'columns'          => in_array(($module_info->list_columns ?? ''), ['1', '2'], true) ? $module_info->list_columns : 'auto',
		'maxWidth'         => (int)($module_info->content_max_width ?? 0) ?: 1100,
		'summaryLength'    => (int)($module_info->summary_length ?? 0) ?: 200,
		'thumbSize'        => (int)($module_info->thumbnail_size ?? 0) ?: 80,
		'relativeLimit'    => (int)($module_info->relative_time_limit ?? 0) ?: 168,
		'pastFormat'       => trim((string)($module_info->date_format_past ?? '')) ?: 'Y.m.d',
		'showScrap'        => ($module_info->show_scrap ?? 'Y') !== 'N',
		'authorLabel'      => in_array(($module_info->author_label ?? ''), ['category', 'board'], true) ? $module_info->author_label : 'none',
		'displayNew'       => ($module_info->display_new ?? 'Y') !== 'N',
		'durationNew'      => (int)($module_info->duration_new ?? 0) ?: 12,
		'displayHot'       => ($module_info->display_hot ?? 'Y') !== 'N',
		'hotVotes'         => (int)($module_info->hot_votes ?? 0) ?: 10,
		'hotComments'      => (int)($module_info->hot_comments ?? 0) ?: 20,
		'pagingMode'       => in_array(($module_info->paging_mode ?? ''), ['paging', 'more'], true) ? $module_info->paging_mode : 'infinite',
		'showVoteBox'      => ($module_info->show_vote_box ?? 'Y') !== 'N',
		'useSocialShare'   => ($module_info->use_social_share ?? 'N') === 'Y',
		'showTopButton'    => ($module_info->show_top_button ?? 'Y') !== 'N',
		'useSticker'       => ($module_info->use_sticker ?? 'Y') !== 'N',
		// 게시판 관리 권한. 설정·게시물 관리 버튼과 카드 체크박스를 이 값 하나로 켠다.
		// comment.html / category.html 처럼 $grant 가 없는 경로에서도 include 되므로 ?? 로 받는다.
		'isManager'        => (bool)($grant->manager ?? false),
	];

	// sticker 모듈은 이 사이트에 항상 있는 게 아니다. 스킨 옵션이 켜져 있어도 모듈 설정이 '사용'이 아니면
	// 버튼을 렌더하지 않는다 (modules/sticker/tpl/config.html:23 의 use 값).
	// getModuleConfig는 모듈이 설치돼 있지 않으면 객체가 아닌 값을 준다.
	if ($cube['useSticker']) {
		$cube_stickerConfig = ModuleModel::getInstance()->getModuleConfig('sticker');
		$cube['useSticker'] = is_object($cube_stickerConfig) && ($cube_stickerConfig->use ?? 'N') === 'Y';
	}

	/*
		목록·상세·댓글이 함께 쓰는 시간 표기.
		기준 시간 안이면 "방금 / N초 전 / N분 전 / N시간 전 / N일 전", 넘으면 날짜로 떨어뜨린다.
	*/
	if (!function_exists('cube_timeago')) {
		function cube_timeago($timestamp, $config)
		{
			$timestamp = (int)$timestamp;
			if (!$timestamp) {
				return '';
			}
			return (RX_TIME - $timestamp) < (($config['relativeLimit'] ?? 168) * 3600)
				? Rhymix\Framework\DateTime::getRelativeTimestamp($timestamp)
				: getDisplayDateTime($timestamp, $config['pastFormat'] ?? 'Y.m.d');
		}
	}

	// 스티커 선택기 하단 링크. getUrl의 첫 인자를 ''로 주면 현재 요청 인자(document_srl·page 등)를
	// 전부 버리고 새로 만든다(Context.class.php:1784) — 게시판 인자가 딸려가지 않게 하려면 이 형태여야 한다.
	// 순서 조정(dispStickerMylist)은 비로그인이면 서버가 막으므로 로그인 상태에서만 내보낸다.
	$cube['stickerStoreUrl'] = $cube['useSticker'] ? getUrl('', 'mid', 'sticker') : '';
	$cube['stickerMylistUrl'] = ($cube['useSticker'] && $is_logged) ? getUrl('', 'mid', 'sticker', 'act', 'dispStickerMylist') : '';
@endphp
