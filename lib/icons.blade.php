{{-- 출력 없음. 인라인 SVG 아이콘을 $cube_icons 배열로 한 번만 만들어 둔다. 카드/댓글처럼 많이 반복되는 곳에서 @include 비용 없이 {!! $cube_icons['eye'] !!} 로 바로 쓴다. --}}
@php
	$cube_icons = [
		'eye' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="M1 8s2.5-4.5 7-4.5S15 8 15 8s-2.5 4.5-7 4.5S1 8 1 8Z"/><circle cx="8" cy="8" r="2"/></svg>',
		'heart' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round" aria-hidden="true"><path d="M8 13.8S1.6 10 1.6 5.7A3.2 3.2 0 0 1 8 4.6a3.2 3.2 0 0 1 6.4 1.1C14.4 10 8 13.8 8 13.8Z"/></svg>',
		'heart-filled' => '<svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 13.8S1.6 10 1.6 5.7A3.2 3.2 0 0 1 8 4.6a3.2 3.2 0 0 1 6.4 1.1C14.4 10 8 13.8 8 13.8Z"/></svg>',
		'comment' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round" aria-hidden="true"><path d="M1.6 8c0-3 2.9-5.4 6.4-5.4s6.4 2.4 6.4 5.4-2.9 5.4-6.4 5.4c-.7 0-1.4-.1-2-.3L2.6 14l.7-2.6C2.1 10.3 1.6 9.2 1.6 8Z"/></svg>',
		'bookmark' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round" aria-hidden="true"><path d="M3.5 1.8h9v12.4L8 11l-4.5 3.2z"/></svg>',
		'bookmark-filled' => '<svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M3.5 1.8h9v12.4L8 11l-4.5 3.2z"/></svg>',
		'clock' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" aria-hidden="true"><circle cx="8" cy="8" r="6.3"/><path d="M8 4.6V8l2.6 1.5"/></svg>',
		'image' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round" aria-hidden="true"><rect x="1.6" y="2.8" width="12.8" height="10.4" rx="1"/><circle cx="5.4" cy="6.4" r="1.1"/><path d="m2.4 12 3.6-4 2.6 2.8 2-2.4 3 3.6"/></svg>',
		'chevron-down' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 6 4 4 4-4"/></svg>',
		'chevron-left' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m10 3-5 5 5 5"/></svg>',
		'chevron-right' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 3 5 5-5 5"/></svg>',
		'close' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path d="M3.5 3.5l9 9M12.5 3.5l-9 9"/></svg>',
		'link' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" aria-hidden="true"><path d="M6.7 9.3 9.3 6.7M6.4 4.2l.9-.9a2.5 2.5 0 0 1 3.5 3.5l-.9.9M9.6 11.8l-.9.9a2.5 2.5 0 0 1-3.5-3.5l.9-.9"/></svg>',
		'flag' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round" aria-hidden="true"><path d="M3.2 1.8v12.4M3.2 2.4h8l-2 3 2 3h-8"/></svg>',
		'dots' => '<svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><circle cx="3.4" cy="8" r="1.3"/><circle cx="8" cy="8" r="1.3"/><circle cx="12.6" cy="8" r="1.3"/></svg>',
		'top' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 12.5v-9M4 7l4-4 4 4"/></svg>',
		'search' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="7" cy="7" r="4.5"/><path d="M10.5 10.5 14 14" stroke-linecap="round"/></svg>',
		'reply' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6.5 4 2.5 8l4 4M2.5 8h6.2a4.3 4.3 0 0 1 4.3 4.3v.4"/></svg>',
		'lock' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round" aria-hidden="true"><rect x="3.2" y="7.2" width="9.6" height="6.6" rx="1"/><path d="M5.2 7.2V5a2.8 2.8 0 0 1 5.6 0v2.2"/></svg>',
		'user' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><circle cx="8" cy="5.4" r="2.6"/><path d="M2.4 14c.6-3 2.8-4.6 5.6-4.6S13 11 13.6 14"/></svg>',
		'clip' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13 7.3 8 12.4a3.1 3.1 0 0 1-4.4-4.4l5.3-5.3a2.1 2.1 0 0 1 2.9 2.9L6.5 10.9a1 1 0 0 1-1.4-1.4l4.6-4.7"/></svg>',
		'gear' =>'<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="8" r="2.2"/><path d="M13 8c0-.4 0-.7-.1-1.1l1.3-1-1.4-2.4-1.5.6a5 5 0 0 0-1.8-1.1L9.2 1.4H6.4l-.3 1.6a5 5 0 0 0-1.8 1.1l-1.5-.6L1.4 5.9l1.3 1a6 6 0 0 0 0 2.2l-1.3 1 1.4 2.4 1.5-.6a5 5 0 0 0 1.8 1.1l.3 1.6h2.8l.3-1.6a5 5 0 0 0 1.8-1.1l1.5.6 1.4-2.4-1.3-1c.1-.4.1-.7.1-1.1Z"/></svg>',
		'check-square' => '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.4 8v4.4a1 1 0 0 1-1 1H3.6a1 1 0 0 1-1-1V3.6a1 1 0 0 1 1-1H10"/><path d="m5.4 7.6 2.4 2.4 6-6.6"/></svg>',
		'smile' =>'<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" aria-hidden="true"><circle cx="8" cy="8" r="6.3"/><path d="M5.4 9.4a3 3 0 0 0 5.2 0"/><path d="M6 6.2v.01M10 6.2v.01" stroke-width="1.8"/></svg>',
	];
@endphp
