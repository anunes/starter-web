<form id="createTableForm" method="post" action="/admin/table/create">
    <?= csrf() ?>

    <div class="mb-3">
        <label for="tableName" class="form-label"><?= t('admin.table_name') ?></label>
        <input type="text" class="form-control" id="tableName" name="table_name" required pattern="^[a-z_][a-z0-9_]*$" placeholder="my_table">
        <div class="form-text"><?= t('admin.table_name_help') ?></div>
    </div>

    <div class="mb-4">
        <label class="form-label"><?= t('admin.table_columns') ?></label>
        <div id="columnsContainer">
            <div class="column-row mb-3 p-3 border rounded">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" class="form-control column-name" name="columns[0][name]" placeholder="<?= t('admin.column_name') ?>" required pattern="^[a-z_][a-z0-9_]*$">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select column-type" name="columns[0][type]" required>
                            <option value="">-- <?= t('admin.column_type') ?> --</option>
                            <option value="VARCHAR(255)"><?= t('admin.column_type_varchar') ?></option>
                            <option value="TEXT"><?= t('admin.column_type_text') ?></option>
                            <option value="INT"><?= t('admin.column_type_int') ?></option>
                            <option value="BIGINT"><?= t('admin.column_type_bigint') ?></option>
                            <option value="DECIMAL(10,2)"><?= t('admin.column_type_decimal') ?></option>
                            <option value="BOOLEAN"><?= t('admin.column_type_boolean') ?></option>
                            <option value="DATETIME"><?= t('admin.column_type_datetime') ?></option>
                            <option value="DATE"><?= t('admin.column_type_date') ?></option>
                            <option value="TIME"><?= t('admin.column_type_time') ?></option>
                            <option value="TIMESTAMP"><?= t('admin.column_type_timestamp') ?></option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mt-2">
                            <input class="form-check-input column-nullable" type="checkbox" name="columns[0][nullable]" id="nullable0">
                            <label class="form-check-label" for="nullable0"><?= t('admin.column_nullable') ?></label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control column-default" name="columns[0][default]" placeholder="<?= t('admin.column_default') ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-danger remove-column" style="display:none;">
                            <i class="bi bi-trash"></i> <?= t('admin.remove_column') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-secondary" id="addColumnBtn">
            <i class="bi bi-plus-circle"></i> <?= t('admin.add_column') ?>
        </button>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="/admin" class="btn btn-secondary"><i class="bi bi-x-circle"></i> <?= t('common.cancel') ?></a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-table"></i> <?= t('admin.create_table_button') ?></button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let columnCount = 1;

        document.getElementById('addColumnBtn').addEventListener('click', function() {
            const container = document.getElementById('columnsContainer');
            const columnRow = document.createElement('div');
            columnRow.className = 'column-row mb-3 p-3 border rounded';
            columnRow.innerHTML = `
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" class="form-control column-name" name="columns[${columnCount}][name]" placeholder="<?= t('admin.column_name') ?>" required pattern="^[a-z_][a-z0-9_]*$">
                </div>
                <div class="col-md-3">
                    <select class="form-select column-type" name="columns[${columnCount}][type]" required>
                        <option value="">-- <?= t('admin.column_type') ?> --</option>
                        <option value="VARCHAR(255)"><?= t('admin.column_type_varchar') ?></option>
                        <option value="TEXT"><?= t('admin.column_type_text') ?></option>
                        <option value="INT"><?= t('admin.column_type_int') ?></option>
                        <option value="BIGINT"><?= t('admin.column_type_bigint') ?></option>
                        <option value="DECIMAL(10,2)"><?= t('admin.column_type_decimal') ?></option>
                        <option value="BOOLEAN"><?= t('admin.column_type_boolean') ?></option>
                        <option value="DATETIME"><?= t('admin.column_type_datetime') ?></option>
                        <option value="DATE"><?= t('admin.column_type_date') ?></option>
                        <option value="TIME"><?= t('admin.column_type_time') ?></option>
                        <option value="TIMESTAMP"><?= t('admin.column_type_timestamp') ?></option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-2">
                        <input class="form-check-input column-nullable" type="checkbox" name="columns[${columnCount}][nullable]" id="nullable${columnCount}">
                        <label class="form-check-label" for="nullable${columnCount}"><?= t('admin.column_nullable') ?></label>
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control column-default" name="columns[${columnCount}][default]" placeholder="<?= t('admin.column_default') ?>">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger remove-column">
                        <i class="bi bi-trash"></i> <?= t('admin.remove_column') ?>
                    </button>
                </div>
            </div>
        `;

            container.appendChild(columnRow);
            columnCount++;
            updateRemoveButtons();
        });

        // Event delegation for remove buttons
        document.getElementById('columnsContainer').addEventListener('click', function(e) {
            if (e.target.closest('.remove-column')) {
                e.preventDefault();
                e.target.closest('.column-row').remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.column-row');
            rows.forEach(row => {
                const btn = row.querySelector('.remove-column');
                btn.style.display = rows.length > 1 ? 'block' : 'none';
            });
        }

        // Initial update
        updateRemoveButtons();
    });
</script>
