<form method="{{ $method }}" action="{{ $action }}" id="form">
    @foreach ($data as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
    @endforeach
</form>

<script>
    document.getElementById('form').submit();
</script>