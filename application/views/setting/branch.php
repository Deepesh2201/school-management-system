<div class="content-wrapper" style="min-height: 348px;">
    <section class="content">
        <div class="row">
            <?php $this->load->view('setting/_settingmenu'); ?>
            <!-- left column -->
            <div class="col-lg-9 col-md-8 col-sm-8">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title titlefix"><i class="fa fa-sitemap"></i> <?php echo $this->lang->line('branch_management'); ?></h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addBranchModal">
                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_branch'); ?>
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="branchTable">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('branch_name'); ?></th>
                            <th><?php echo $this->lang->line('base_url'); ?></th>
                            <th><?php echo $this->lang->line('folder_path'); ?></th>
                            <th><?php echo $this->lang->line('status'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($branches as $branch): ?>
                        <tr>
                            <td><?php echo html_escape($branch->name); ?></td>
                            <td><?php echo html_escape($branch->base_url); ?></td>
                            <td><?php echo html_escape($branch->folder_path); ?></td>
                            <td>
                                <span class="label label-<?php echo $branch->is_active ? 'success' : 'danger'; ?>">
                                    <?php echo $branch->is_active ? $this->lang->line('active') : $this->lang->line('inactive'); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Add Branch Modal -->
<div class="modal fade" id="addBranchModal" tabindex="-1" role="dialog" aria-labelledby="addBranchModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addBranchModalLabel"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_branch'); ?></h4>
            </div>
            <form id="addBranchForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name"><?php echo $this->lang->line('branch_name'); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="<?php echo $this->lang->line('enter_branch_name'); ?>" required>
                        <span class="text-danger" id="nameError"></span>
                    </div>
                    <div class="form-group">
                        <label for="base_url"><?php echo $this->lang->line('base_url'); ?> <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="base_url" name="base_url" placeholder="http://yourdomain.com/branch1/" required>
                        <span class="text-danger" id="baseUrlError"></span>
                    </div>
                    <div class="form-group">
                        <label for="folder_path"><?php echo $this->lang->line('folder_path'); ?></label>
                        <input type="text" class="form-control" id="folder_path" name="folder_path" placeholder="/var/www/branch1/">
                        <span class="text-danger" id="folderPathError"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#branchTable').DataTable({
        "order": [[0, "asc"]],
        "pageLength": 25
    });

    $('#addBranchForm').on('submit', function (e) {
        e.preventDefault();
        var $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        $('.text-danger').text('');

        $.ajax({
            url: '<?php echo site_url('schsettings/branch_add'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).html('Save');
                if (res.success) {
                    $('#addBranchModal').modal('hide');
                    $('#addBranchForm')[0].reset();
                    successMsg(res.msg);
                    setTimeout(function () { location.reload(); }, 1000);
                } else {
                    errorMsg(res.msg);
                    if (res.error) {
                        if (res.error.name) $('#nameError').text(res.error.name);
                        if (res.error.base_url) $('#baseUrlError').text(res.error.base_url);
                        if (res.error.folder_path) $('#folderPathError').text(res.error.folder_path);
                    }
                }
            },
            error: function () {
                $btn.prop('disabled', false).html('Save');
                errorMsg('An error occurred. Please try again.');
            }
        });
    });
});
</script>