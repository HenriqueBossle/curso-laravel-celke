import './bootstrap';

window.confirmDelete = function (id){
    Swal.fire({
    title: "Tem certeza?",
    text: "Você não poderá reverter isso!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#ff0000ff",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sim, apagar!",
    cancelButtonText: "Cancelar",
    }).then((result)=>{
      if(result.isConfirmed){
        document.getElementById('delete-form-' + id).submit();
      }
    })
}
