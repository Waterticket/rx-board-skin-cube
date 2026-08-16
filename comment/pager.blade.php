{{-- 댓글 페이지네이션. document.item.php:1067 의 $oDocument->comment_page_navigation. ../ui 는 comment/ 기준 형제 디렉토리로 가는 경로. --}}
@include('../ui/pagination', ['nav' => $oDocument->comment_page_navigation ?? null, 'param' => 'cpage'])
