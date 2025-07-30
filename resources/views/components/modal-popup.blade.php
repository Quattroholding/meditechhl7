<div id="delete_modal" class="modal fade delete-modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" id="form_delete">
        @csrf
        @method('DELETE')
        <div class="modal-content">
            <div class="modal-body text-center">
                <img src="{{ URL::asset('/assets/img/sent.png') }}" alt="" width="50" height="46">
                <h3>{{__('Estas seguro que quieres borrar esto')}}?</h3>

                <div class="flex items-center justify-end">
                    <div class="doctor-submit text-end py-3">
                        <button type="submit" class="btn btn-danger">{{ __('button.delete') }} </button>
                        <a class="btn btn-secondary" data-bs-dismiss="modal">{{ __('button.cancel') }}</a>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
<script>
    const deleteModal = document.getElementById('delete_modal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        // Get the button that triggered the modal
        const button = event.relatedTarget;

        // Extract info from data-item-id attribute
        const route = button.getAttribute('data-route');


        // Update the modal's content
        const form_delete = deleteModal.querySelector('#form_delete');

        form_delete.action = route; // Or update an input field, etc.
    });
</script>
