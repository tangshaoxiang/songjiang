<table rules="rows">
    @foreach($data as $val)
        <tr>
            <td width="200px"><input type="checkbox" class="form-check-input parent" name="p_id[]"
                                     value="{{$val->p_id}}" <?php if ($val->checked == 1) {
                    echo 'checked';
                } ?>>{{$val->p_name}}</td>
            @if(isset($val->son))
                <td>
                    @foreach($val->son as $k=>$v)
                        <input type="checkbox" class="form-check-input son" name="p_id[]"
                               value="{{$v->p_id}}" <?php if ($v->checked == 1) {
                            echo 'checked';
                        } ?>>{{$v->p_name}}&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                    @endforeach
                </td>
            @endif
        </tr>
    @endforeach
</table>
<script>
    //点击父级,子集全部选中
    $('.parent').click(function () {
        if ($(this).prop("checked")) {
            $(this).parent().next().find('.son').prop('checked', true)
        } else {
            $(this).parent().next().find('.son').prop('checked', false)
        }
        //简写
        //$(this).parent().next().find('.son').prop('checked',$(this).prop("checked"))
    })
    //点击子集父级必选,切子集没有一个选中的话,父级也不选中
    $('.son').click(function () {
        if ($(this).prop('checked')) {
            $(this).parent().prev().find('.parent').prop('checked', true)
        } else if (!$(this).siblings('input:checked').val()) {
            $(this).parent().prev().find('.parent').prop('checked', false)
        }

    })

</script>

