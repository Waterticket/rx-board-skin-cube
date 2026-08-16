{{-- 진입점. board.view.php:230 dispBoardContent() -> setTemplateFile('list') 가 호출한다. --}}
@include('lib/boot')
@include('layout/assets')
@include('layout/open')

@if($oDocument->isExists())
	@include('read/index')
@else
	@include('list/index')
@endif

@include('layout/close')
