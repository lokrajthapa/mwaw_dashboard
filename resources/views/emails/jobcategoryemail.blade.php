Button Text
<h1> </h1>

@foreach ($categories as $category )

{{ $category->name }}::{{ $category->jobs_count }}
    
@endforeach
