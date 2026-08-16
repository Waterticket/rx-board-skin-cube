{{-- 첨부파일 목록. board.view.php:474 의 $file_list. --}}
{{-- 접힌 상태가 기본이다. 라이믹스는 원본 파일명이 아니라 해시로 저장된 이름이 그대로 나오는 경우가 있어(40자짜리 hex), 펼쳐 두면 본문 아래에 정체불명의 긴 문자열만 한 줄 남는다. 모바일에서는 그게 화면 폭을 다 먹는다. --}}
{{-- <details>/<summary> 네이티브 요소라 여는 동작에 JS가 필요 없다 — 스킨의 ⋯ 드롭다운(read/head.blade.php)과 같은 방식. --}}
@if(!empty($file_list))
<details class="cube-files">
	<summary class="cube-files-summary">
		{!! $cube_icons['clip'] !!}
		<span>첨부파일 {{ count($file_list) }}</span>
		<i class="cube-files-caret" aria-hidden="true">{!! $cube_icons['chevron-down'] !!}</i>
	</summary>
	<ul class="cube-files-list">
		@foreach($file_list as $cube_file)
			<li class="cube-file-item">
				<a href="{{ getUrl('') }}{{ $cube_file->download_url }}">{{ $cube_file->source_filename }}</a>
				<span class="cube-file-size">{{ FileHandler::filesize($cube_file->file_size) }}</span>
			</li>
		@endforeach
	</ul>
</details>
@endif
