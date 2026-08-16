{{-- 상세 화면 조립. list.blade.php가 $oDocument->isExists()===true 일 때 include 한다. 경로는 이 파일이 있는 read/ 기준, comment/ 는 형제 디렉토리라 ../ 를 거친다. --}}
{{-- board.view.php의 dispBoardContentView()(list 액션이 타는 경로)는 $comment_list·$file_list를 세팅하지 않는다 — 각각 dispBoardContentCommentList()/dispBoardContentFileList()라는 별도 액션에서만 채워진다(실제로 어떤 코어 스킨도 두 값을 list 경로에서 그냥 쓰지 않는다). 그래서 여기서 없으면 직접 채운다. --}}
@php
	if (!isset($comment_list)) {
		$comment_list = $oDocument->getComments();
	}
	if (!isset($file_list)) {
		$file_list = $oDocument->getUploadedFiles();
	}
@endphp
<div class="cube-wrap cube-read-wrap">
	@include('head')
	@include('body')
	@include('files')
	@include('actions')
	@include('../comment/index')
	@include('nav')
</div>
