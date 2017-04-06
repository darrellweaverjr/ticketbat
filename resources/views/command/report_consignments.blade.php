@if($format == 'csv')
"SHOW: ","{{$c->show_name}}"
"DATE/TIME: ","{{date('m/d/Y g:ia',strtotime($c->show_time))}}"
"SELLER: ","{{$c->seller}}"
" "
"#","SECTION / ROW","SEAT","STATUS"
@foreach ($c->seats as $n => $s)
"{{$n+1}}","{{$s->ticket_type}}","{{$s->seat}}","{{$s->status}}"
@endforeach
@endif