@props([
'url',
'color' => 'primary',
'align' => 'center',
])

<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<a href="{{ $url }}"
style="
background:#2563eb;
border-radius:8px;
color:#ffffff;
display:inline-block;
font-weight:600;
padding:12px 28px;
text-decoration:none;
font-size:15px;
"
target="_blank">
{!! $slot !!}
</a>
</td>
</tr>
</table>
</td>
</tr>
</table>