{{-- 댓글 영역 전체. read/index.blade.php 와 comment.html(dispBoardCommentPage) 양쪽에서 include 된다. 경로는 이 파일이 있는 comment/ 기준. --}}
<section class="cube-comments" id="comment" data-document-srl="{{ $oDocument->document_srl }}">
	@include('head')
	@include('form')
	@include('list')
	@include('pager')
</section>
