<div class="modal fade" id="multiBranchSwitchModal" tabindex="-1" role="dialog" aria-labelledby="multiBranchSwitchModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="multiBranchSwitchModalLabel"><?php echo $this->lang->line('switch_branch'); ?></h4>
            </div>
            <div class="modal-body">
                <p class="text-muted">
                    <?php echo $this->lang->line('current_session'); ?> / <?php echo $this->lang->line('branch'); ?>:
                    <strong><?php echo html_escape($this->customlib->getCurrentBranchName()); ?></strong>
                </p>
                <div class="list-group">
                    <?php
                    $current_branch = $this->customlib->getCurrentBranchId();
                    foreach ($this->customlib->getAllowedBranches() as $b) {
                        $is_active = ((int) $b['id'] === (int) $current_branch);
                        ?>
                        <a class="list-group-item <?php echo $is_active ? 'active' : ''; ?>"
                           href="<?php echo site_url('admin/branch/switch_branch/' . $b['id']); ?>">
                            <i class="fa fa-building-o"></i> <?php echo html_escape($b['name']); ?>
                            <?php if ($is_active) { ?>
                                <span class="pull-right label label-primary"><?php echo $this->lang->line('active'); ?></span>
                            <?php } else { ?>
                                <span class="pull-right"><i class="fa fa-exchange"></i></span>
                            <?php } ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
