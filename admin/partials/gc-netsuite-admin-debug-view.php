<?php
global $wpdb;
$table_name = $wpdb->prefix . GC_NETSUITE_TABLE_NAME;
if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) : ?>
    <p>Database table (<?= $table_name ?>) for logging does not exist. direct 3</p>
<?php else :
    $query = 'SELECT * FROM ' . $table_name . ' ORDER BY submitted DESC';
    $logs = $wpdb->get_results($query);
    if ($logs) :
        ?>
        <a href="#debug" id="show-debug">Show Logs</a>
        <div id="debug" style"display:none">
        <table>
            <thead>
            <th>Submitted</th>
            <th>URL</th>
            <th>Data</th>
            <th>Response</th>
            <th>Success</th>
            <th>Error</th>
            <th>Message</th>
            </thead>
            <tbody>
            <?php foreach($logs as $log) : ?>
                <tr>
                    <td><?= $log->submitted ?></td>
                    <td><?= $log->url ?></td>
                    <td><?= $log->data ?></td>
                    <td><?= $log->response ?></td>
                    <td><?= $log->success ?></td>
                    <td><?= $log->error ?></td>
                    <td><?= $log->message ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        </div>
        <script>
            $('#show-debug').click(function () {
                $('#debug').slideToggle();
                var $this = $(this);
                if ($this.html() === 'Show Logs') {
                    $this.html('Hide Logs');
                } else {
                    $this.html('Show Logs');
                }
            })
        </script>
    <?php else : ?>
        <p style="font-size: 0.85rem;">No logs available.</p>
    <?php endif; endif;