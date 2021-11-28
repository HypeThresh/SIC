@extends('layouts.app')

@section('content')

<form class="partida shadow rounded-3" action="/item/{{$item->id}}" method="post" name="item">
    @csrf
    @method('put')
    <table class="" id="table">
        <tr class="">
            <th class="fecha"></th>
            <th class="concepto">Cuenta</th>
            <th class="debe">Debe</th>
            <th class="haber">Haber</th>
        </tr>
            @for ($i = 0; $i < count($parts); $i++)
            <tr>
                @if($i == 0)
                    <td class="fecha">
                        <input  class="" type="date" name="date" id=""  value="{{$date}}" >
                    </td>
                @else
                    <td class="fecha">
                        <input  class="input-fecha" type="date" name="" id="" style="color: #f8f9fa;" readonly>
                    </td>
                @endif
                <td class="concepto">
                    <select class="selectpicker my-2" data-live-search="true" data-width="100%" name="account{{$i+1}}">
                        @foreach($accounts as $account)
                                @if ($account->id == $parts[$i]->account_id)
                                <option value="{{$account->id}},{{$account->title}}" selected>{{$account->id}} {{$account->title}}</option> 
                                @else
                                <option value="{{$account->id}},{{$account->title}}">{{$account->id}} {{$account->title}}</option>  
                                @endif
                                 
                        @endforeach
                    </select>
                </td>
                <td class="debe">
                    @if($parts[$i]->debit >0)
                        <input class="debe-input" type="number" name="debe{{$i+1}}" id=""  min="0" step="0.01" value="{{$parts[$i]->debit}}">
                    @else
                        <input class="debe-input" type="number" name="debe{{$i+1}}" id=""  min="0" step="0.01">   
                    @endif
                </td>
                <td class="haber">
                    @if($parts[$i]->credit >0)
                        <input class="haber-input" type="number" name="haber{{$i+1}}" id=""  min="0" step="0.01" value="{{$parts[$i]->credit}}">
                    @else
                        <input class="haber-input" type="number" name="haber{{$i+1}}" id=""  min="0" step="0.01">   
                    @endif
                </td>
            </tr>
            @endfor
    </table>
    <table id="table">
        <tr>
            <td class="descripcion">
                <input type="text" name="description" class="input-descripcion" placeholder="Descripción de la partida" value="{{$item->description}}">
            </td>
        </tr>
        <tr>
            <td class="fecha invisible">
            <input  class="" type="date" name="" id="" readonly>
            </td>
            <td class="concepto invisible">
               
            </td>
            <td class="totales">
                <input class="text-success" type="number" name="" id="total-debe"  min="0" value="0" step="0.01" readonly>
            </td>
            <td class="totales">
                <input class="text-success" type="number" name="" id="total-haber"  min="0" value="0" step="0.01" readonly>
            </td>
        </tr>
    </table>
    <div class="div-botones mt-3">
        <button id="plus" class="btn btn-primary rounded-circle mx-2 shadow btnAdd" type="button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar cuenta"><i class="fas fa-plus"></i></button>
        <button id="check" class="btn btn-success rounded-circle mx-2 shadow btnAdd" type="submit"  data-bs-toggle="tooltip" data-bs-placement="top" title="Actualizar partida"><i class="fas fa-check-double"></i></button>
        <button id="minus" class="btn btn-primary rounded-circle mx-2 shadow btnAdd" type="button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Quitar cuenta"><i class="fas fa-minus"></i></button>
    </div>
    <div class="error text-danger text-center mt-3" style="font-weight: 500;"></div>
</form>
@endsection
@section('js')
<script> 

let cont = <?php echo count($parts); ?>

$(document).ready(function (){

    $('.selectpicker').selectpicker('render')
    
    let sumadebe = 0
    let sumahaber = 0

    for (let i = 1; i <= cont; i++) {

        let debe = parseFloat($(`input[name=debe${i}]`).val())
        let haber = parseFloat($(`input[name=haber${i}]`).val()) 

        if (Number.isNaN(debe)) {
            debe = 0
        }
        if (Number.isNaN(haber)) {
            haber = 0    
        }  

        sumadebe += debe
        sumahaber += haber   
    }

    $('#total-debe').val(sumadebe)
    $('#total-haber').val(sumahaber)
    

    $("#plus").click(function() {
        cont +=1
        console.log(cont)
        $('#table').append(`
            <tr>
                <td class="fecha">
                <input  class="input-fecha" type="date" name="" id=""  style="color: #f8f9fa;" readonly>
                </td>
                <td class="concepto">
                    <select class="selectpicker my-2" data-live-search="true" data-width="100%" name="account${cont}">
                        @foreach($accounts as $account)
                                <option value="{{$account->id}},{{$account->title}}">{{$account->id}} {{$account->title}}</option>  
                        @endforeach
                    </select>
                </td>
                <td class="debe">
                    <input class="debe-input" type="number" name="debe${cont}" id=""  min="0"  step="0.01">
                </td>
                <td class="haber">
                    <input class="haber-input" type="number" name="haber${cont}" id=""  min="0"  step="0.01">
                </td>
            </tr>     
        `)

        $('.selectpicker').selectpicker('render')
        
    })

    $("#minus").click(function(){

        if(cont>2){
            let n = $("#table").find("tr:last").find(".debe-input").val()
            let n1 = $("#table").find("tr:last").find(".haber-input").val()

            if($('#total-debe').val() > 0){
                $('#total-debe').val(parseFloat($('#total-debe').val())-parseFloat("0"+n))
            }

            if($('#total-haber').val() > 0){
                $('#total-haber').val(parseFloat($('#total-haber').val())-parseFloat("0"+n1))
                console.log($('#total-haber').val())
            }
            correctbalance()
            $("#table").find("tr:last").remove()
            cont -=1;
        }
    })

    $(document).on('change','.debe-input', function(){ 
        let suma = 0
        $(".debe-input").each(function() {
            suma += parseFloat("0"+$(this).val())
        })
        $('#total-debe').val(suma)
        correctbalance()
    }).change()

    $(document).on('change','.haber-input', function(){
        let suma = 0
        $(".haber-input").each(function() {
            suma += parseFloat("0"+$(this).val())
        })
        $('#total-haber').val(suma)
        correctbalance()
    }).change()
    
    function correctbalance(){
        let totaldebe = $('#total-debe').val()
        let totalhaber = $('#total-haber').val()
        if( totaldebe != totalhaber){
            $('#total-debe').addClass("text-danger")
            $('#total-debe').removeClass("tex-success")
            $('#total-haber').addClass("text-danger")
            $('#total-haber').removeClass("tex-success")
        }
        else{
            $('#total-debe').addClass("text-success")
            $('#total-debe').removeClass("text-danger")
            $('#total-haber').addClass("tex-success")
            $('#total-haber').removeClass("text-danger")
        }
    }

    $("form").submit(function(event) {

        for (let i = 1; i <= cont; i++) {
            let debe = parseFloat($(`input[name=debe${i}]`).val())
            let haber = parseFloat($(`input[name=haber${i}]`).val())  
            if (Number.isNaN(debe)) {
                $(`input[name=debe${i}]`).val(0)
            }
            if (Number.isNaN(haber)) {
                $(`input[name=haber${i}]`).val(0)
            }

            if(debe > 0 && haber > 0){
                $('.error').text('Cada cuenta debe tener un valor en debe o haber, no en ambos')
                event.preventDefault()
            }
        }

        let totaldebe = $('#total-debe').val()
        let totalhaber = $('#total-haber').val()
        let descripcion = $('.input-descripcion').val()
        if(totaldebe != totalhaber){
            $('.error').text('La partida debe estar en balance')
            event.preventDefault()
        }
        else if(totaldebe == 0 || totalhaber == 0){
            $('.error').text('El balance de la partida debe ser mayor a cero')
            event.preventDefault()
        }
        else if(descripcion.length < 1){
            $('.error').text('La descripcion no puede estar vacia')
            event.preventDefault()    
        }
        else{
            return
        }
    })
    

})

</script>
@endsection
