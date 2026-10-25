<div class="content-wrapper" style="min-height: 946px;">
    <section class="content">
        <div class="row">
            <?php if ($this->session->flashdata('branch_success')) { ?>
                <div class="col-md-12">
                    <div class="alert alert-success"><?php echo $this->session->flashdata('branch_success'); ?></div>
                </div>
            <?php } ?>
            <?php if ($this->session->flashdata('branch_error')) { ?>
                <div class="col-md-12">
                    <div class="alert alert-danger"><?php echo $this->session->flashdata('branch_error'); ?></div>
                </div>
            <?php } ?>

            <?php if ($this->rbac->hasPrivilege('branch', 'can_add')) { ?>
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-plus"></i> Add Branch</h3>
                    </div>
                    <form action="<?php echo site_url('admin/branch/add'); ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Branch Name <small class="req">*</small></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Database Name <small class="req">*</small></label>
                                <input type="text" name="database_name" class="form-control" required>
                                <small class="text-muted">A separate schema on the same server (or another host below).</small>
                            </div>
                            <div class="form-group">
                                <label>DB Host</label>
                                <input type="text" name="hostname" class="form-control" value="127.0.0.1">
                            </div>
                            <div class="form-group">
                                <label>DB Username</label>
                                <input type="text" name="db_username" class="form-control" value="root">
                            </div>
                            <div class="form-group">
                                <label>DB Password</label>
                                <input type="text" name="db_password" class="form-control" value="">
                            </div>
                            <div class="form-group">
                                <label>Base URL</label>
                                <input type="text" name="base_url" class="form-control" placeholder="http://localhost:8000/">
                            </div>
                            <div class="form-group">
                                <label>Folder Path</label>
                                <input type="text" name="folder_path" class="form-control" value="">
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php } ?>

            <div class="col-md-<?php echo $this->rbac->hasPrivilege('branch', 'can_add') ? '8' : '12'; ?>">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title"><i class="fa fa-building"></i> Branches</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Branch</th>
                                    <th>Database</th>
                                    <th>Base URL</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo html_escape($main_branch_name); ?> <span class="label label-success">Main</span></td>
                                    <td><em>default</em></td>
                                    <td>-</td>
                                    <td class="text-right"><span class="text-muted">current</span></td>
                                </tr>
                                <?php foreach ($branches as $b) { ?>
                                <tr>
                                    <td><?php echo html_escape($b->name); ?></td>
                                    <td><?php echo html_escape($b->database_name); ?></td>
                                    <td><?php echo html_escape($b->base_url); ?></td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-primary btn-xs js-branch-edit"
                                            data-id="<?php echo $b->id; ?>"
                                            data-name="<?php echo html_escape($b->name); ?>"
                                            data-database_name="<?php echo html_escape($b->database_name); ?>"
                                            data-hostname="<?php echo html_escape($b->hostname); ?>"
                                            data-db_username="<?php echo html_escape($b->username); ?>"
                                            data-db_password="<?php echo html_escape($b->password); ?>"
                                            data-base_url="<?php echo html_escape($b->base_url); ?>"
                                            data-folder_path="<?php echo html_escape($b->folder_path); ?>"
                                            data-toggle="modal" data-target="#branchEditModal"
                                            title="Edit"><i class="fa fa-pencil"></i></button>
                                        <a onclick="return confirm('Delete this branch? Users mapped to it will be unmapped.');"
                                           class="btn btn-primary btn-xs"
                                           href="<?php echo site_url('admin/branch/delete/' . $b->id); ?>"
                                           title="Delete"><i class="fa fa-remove"></i></a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($this->rbac->hasPrivilege('branch', 'can_edit')) { ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-user"></i> Assign User to Branch</h3>
                    </div>
                    <form action="<?php echo site_url('admin/branch/assign'); ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>User (login email) <small class="req">*</small></label>
                                        <select name="username" class="form-control" required>
                                            <option value="">Select</option>
                                            <?php foreach ($staff_list as $s) { ?>
                                                <option value="<?php echo html_escape($s['email']); ?>">
                                                    <?php echo html_escape($s['name'] . ' (' . $s['email'] . ')'); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label>Allowed Branches</label>
                                        <div>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="branch_ids[]" value="0">
                                                <?php echo html_escape($main_branch_name); ?> (Main)
                                            </label>
                                            <?php foreach ($branches as $b) { ?>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="branch_ids[]" value="<?php echo $b->id; ?>">
                                                    <?php echo html_escape($b->name); ?>
                                                </label>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Default Branch (on login)</label>
                                        <select name="default_branch_id" class="form-control">
                                            <option value="0"><?php echo html_escape($main_branch_name); ?> (Main)</option>
                                            <?php foreach ($branches as $b) { ?>
                                                <option value="<?php echo $b->id; ?>"><?php echo html_escape($b->name); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Save</button>
                        </div>
                    </form>
                </div>
                <?php } ?>

                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title"><i class="fa fa-list"></i> Current Assignments</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr><th>User</th><th>Allowed Branches</th><th>Default</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($branch_users)) { ?>
                                    <tr><td colspan="3" class="text-center text-muted">No assignments yet.</td></tr>
                                <?php } ?>
                                <?php foreach ($branch_users as $uname => $info) { ?>
                                    <tr>
                                        <td><?php echo html_escape($uname); ?></td>
                                        <td>
                                            <?php
                                            $labels = array();
                                            foreach ($info['branches'] as $bid) {
                                                $labels[] = ($bid === 0) ? $main_branch_name . ' (Main)' : '#' . $bid;
                                            }
                                            echo html_escape(implode(', ', $labels));
                                            ?>
                                        </td>
                                        <td><?php echo $info['default'] === 0 ? html_escape($main_branch_name) . ' (Main)' : '#' . (int) $info['default']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="branchEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="" method="post" id="branchEditForm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">Edit Branch</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group"><label>Branch Name</label><input type="text" name="name" id="e_name" class="form-control" required></div>
                    <div class="form-group"><label>Database Name</label><input type="text" name="database_name" id="e_database_name" class="form-control" required></div>
                    <div class="form-group"><label>DB Host</label><input type="text" name="hostname" id="e_hostname" class="form-control"></div>
                    <div class="form-group"><label>DB Username</label><input type="text" name="db_username" id="e_db_username" class="form-control"></div>
                    <div class="form-group"><label>DB Password</label><input type="text" name="db_password" id="e_db_password" class="form-control"></div>
                    <div class="form-group"><label>Base URL</label><input type="text" name="base_url" id="e_base_url" class="form-control"></div>
                    <div class="form-group"><label>Folder Path</label><input type="text" name="folder_path" id="e_folder_path" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).on('click', '.js-branch-edit', function () {
        var $b = $(this);
        $('#branchEditForm').attr('action', '<?php echo site_url('admin/branch/edit'); ?>/' + $b.data('id'));
        $('#e_name').val($b.data('name'));
        $('#e_database_name').val($b.data('database_name'));
        $('#e_hostname').val($b.data('hostname'));
        $('#e_db_username').val($b.data('db_username'));
        $('#e_db_password').val($b.data('db_password'));
        $('#e_base_url').val($b.data('base_url'));
        $('#e_folder_path').val($b.data('folder_path'));
    });
</script>
