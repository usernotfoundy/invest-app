@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="./public/assets/logo.webp" class="logo" alt="INvest Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
