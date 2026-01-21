<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger bg-opacity-10 border-danger">
                <h1 class="modal-title fs-5" id="deleteUserModalLabel">
                    <i class="bi bi-exclamation-triangle text-danger"></i> <?= t('admin.delete_modal_title') ?>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= t('admin.delete_modal_message') ?></p>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-danger h-100">
                            <div class="card-body">
                                <h6 class="card-title text-danger">
                                    <i class="bi bi-exclamation-circle"></i> <?= t('admin.delete_permanent') ?>
                                </h6>
                                <p class="card-text small text-muted">
                                    <?= t('admin.delete_permanent_warning') ?>
                                </p>
                                <button type="button" class="btn btn-danger btn-sm w-100" id="deletePermanentBtn">
                                    <i class="bi bi-trash-fill"></i> <?= t('admin.delete_permanent') ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-warning h-100">
                            <div class="card-body">
                                <h6 class="card-title text-warning">
                                    <i class="bi bi-pause-circle"></i> <?= t('admin.delete_inactive') ?>
                                </h6>
                                <p class="card-text small text-muted">
                                    <?= t('admin.delete_inactive_warning') ?>
                                </p>
                                <button type="button" class="btn btn-warning btn-sm w-100" id="deleteInactiveBtn">
                                    <i class="bi bi-person-dash"></i> <?= t('admin.delete_inactive') ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> <?= t('admin.cancel_delete') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        let currentDeleteUrl = null;

        // Store delete URL when modal is opened
        const deleteUserModalElement = document.getElementById('deleteUserModal');
        deleteUserModalElement.addEventListener('show.bs.modal', function(e) {
            currentDeleteUrl = e.relatedTarget.getAttribute('href');
        });

        // Permanent delete button
        document.getElementById('deletePermanentBtn').addEventListener('click', function() {
            if (currentDeleteUrl) {
                window.location.href = currentDeleteUrl + '?permanent=1';
            }
        });

        // Soft delete (mark as inactive) button
        document.getElementById('deleteInactiveBtn').addEventListener('click', function() {
            if (currentDeleteUrl) {
                window.location.href = currentDeleteUrl + '?permanent=0';
            }
        });

        // Handle delete button clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-user-trigger') ||
                (e.target.tagName === 'I' && e.target.parentElement.classList.contains('delete-user-trigger'))) {
                e.preventDefault();
                const trigger = e.target.classList.contains('delete-user-trigger') ? e.target : e.target.parentElement;
                const deleteUrl = trigger.getAttribute('href');
                deleteUserModalElement.setAttribute('data-delete-url', deleteUrl);
                deleteModal.show();
            }
        });
    });
</script>
