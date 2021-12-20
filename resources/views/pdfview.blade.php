<a href="{{ route('pdfview',['download'=>'pdf']) }}">Download PDF</a>
<div>
  <center>
  <h1>
    LAPORAN PENGADUAN
  </h1>
  </center>
</div>
@foreach ($items as $key => $item)
<div>
<br/>
<img src="{{url('/image/pengaduan/'.$item->foto)}}" height="150px" alt="logo"/>
  <br/>
</div>
<div style="clear:both"></div>
<div>
  <p>
    Pelapor : {{ $item->user->name }} <br/>
	Tanggal : {{ $item->created_at }} <br/><br/>
    {{ $item->laporan }}
  </p>
</div>
<div>
  <h1>
    Tanggapan
  </h1>
@foreach ($item->tanggapan as $key => $tanggapan)
<div>
  <p>
    Petugas : {{$tanggapan->user->name}} <br/>
	Tanggal : {{ $tanggapan->created_at }} <br/><br/>
    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum
  </p>
    <br/><br/>
</div>
@endforeach
@endforeach
</div>