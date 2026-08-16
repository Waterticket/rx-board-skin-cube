{{-- board.view.php dispBoardWrite() -> setTemplateFile('write_form'). 새 글/수정 공용($oDocument->isExists()로 분기). procFilter(this, window.insert)는 board.view.php가 addJsFilter()로 미리 주입해 둔 코어 검증 함수를 그대로 쓴다. --}}
@include('lib/boot')
@include('layout/assets')
@include('layout/open')

<div class="cube-wrap cube-form-wrap">
	<h1 class="cube-form-title">{{ $oDocument->isExists() ? '글 수정' : '글쓰기' }}</h1>

	<form action="./" method="post" id="cube-write-form" class="cube-form" onsubmit="return procFilter(this, window.insert)">
		<input type="hidden" name="mid" value="{{ $mid }}" />
		<input type="hidden" name="document_srl" value="{{ $document_srl }}" />
		{{-- 에디터 스킨 JS(예: modules/editor/skins/froala/editor.html:78-80)가 같은 폼 안에서 name=content 인 input/textarea를 찾아 그 값으로 에디터를 채운다. 이 필드가 없으면 수정 화면의 에디터가 항상 빈 채로 뜬다. getContentText()는 이미 escape()된 값을 돌려주므로 |noescape로 출력한다. --}}
		<input type="hidden" name="content" value="{!! $oDocument->getContentText() !!}" />

		@if(!$is_logged)
			<div class="cube-form-row cube-form-row-split">
				<input type="text" name="nick_name" class="cube-form-input" value="{{ $oDocument->getNickName() }}" placeholder="닉네임" />
				<input type="password" name="password" class="cube-form-input" placeholder="비밀번호" />
			</div>
		@endif

		@if(($module_info->use_category ?? 'N') === 'Y' && !empty($category_list))
			<div class="cube-form-row">
				<select name="category_srl" class="cube-form-select">
					<option value="">카테고리 선택</option>
					@foreach($category_list as $cube_cat_srl => $cube_cat)
						<option value="{{ $cube_cat_srl }}" @disabled(empty($cube_cat->grant)) @selected((!empty($cube_cat->grant) && !empty($cube_cat->selected)) || $oDocument->get('category_srl') == $cube_cat_srl)>
							{{ str_repeat('　', (int)$cube_cat->depth) }}{{ $cube_cat->title }}
						</option>
					@endforeach
				</select>
			</div>
		@endif

		<div class="cube-form-row">
			<input type="text" name="title" class="cube-form-input cube-form-title-input" value="{{ $oDocument->getTitleText() }}" placeholder="제목" />
		</div>

		@if(!empty($extra_keys))
			@foreach($extra_keys as $cube_ek_key => $cube_ek)
				<div class="cube-form-row">
					<label class="cube-form-label">{{ $cube_ek->name }}@if($cube_ek->is_required === 'Y')<em class="cube-form-required">*</em>@endif</label>
					{!! $cube_ek->getFormHTML() !!}
				</div>
			@endforeach
		@endif

		@if(($module_info->write_tag ?? 'N') === 'Y')
			<div class="cube-form-row">
				<input type="text" name="tags" class="cube-form-input" value="{{ $oDocument->get('tags') }}" placeholder="태그 (쉼표로 구분)" />
			</div>
		@endif

		<div class="cube-form-row cube-form-editor">
			{!! $oDocument->getEditor() !!}
		</div>

		<div class="cube-form-row cube-form-options">
			@if($grant->manager ?? false)
				<label><input type="radio" name="is_notice" value="N" @checked(($oDocument->get('is_notice') ?? 'N') === 'N') /> 일반</label>
				<label><input type="radio" name="is_notice" value="Y" @checked($oDocument->get('is_notice') === 'Y') /> 공지</label>
			@endif
			<label><input type="checkbox" name="comment_status" value="ALLOW" @checked($oDocument->allowComment()) /> 댓글 허용</label>
			@if($is_logged)
				<label><input type="checkbox" name="notify_message" value="Y" @checked(($module_info->notify_comment ?? 'N') === 'Y' || $oDocument->useNotify()) /> 댓글 알림</label>
			@endif
		</div>

		@if(is_array($status_list ?? null))
			<div class="cube-form-row cube-form-options">
				@foreach($status_list as $cube_st_key => $cube_st_label)
					<label>
						<input type="radio" name="status" value="{{ $cube_st_key }}" @checked($oDocument->get('status') == $cube_st_key || ($cube_st_key === 'PUBLIC' && !$document_srl)) />
						{{ $cube_st_label }}
					</label>
				@endforeach
			</div>
		@endif

		<div class="cube-form-foot">
			<a class="cube-btn" href="{{ getUrl('document_srl', $document_srl ?: '', 'act', '') }}">취소</a>
			<button type="submit" class="cube-btn cube-btn-primary">{{ $oDocument->isExists() ? '수정' : '등록' }}</button>
		</div>
	</form>
</div>

@include('layout/close')
