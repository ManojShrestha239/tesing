<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="container">
    <table>
        <tr>
            <th> Title</th>
        </tr>
        <tr>  @foreach ($notices as $notice)
        <td>{{ $notice->title }}</td>
              
        @endforeach
        </tr>
    </table>
  
    {{ $notices->links() }}
  
</div>
</body>
</html>