<?php
/**
 * @var \Cake\View\AjaxView $this
 * @var array               $calls
 */
$this->loadHelper('DebugHttp.Call');
?>
<?php if (count($calls) == 0): ?>
    <p class="info"><?= __d('debug_http', 'There were no calls made this request') ?></p>
<?php else: ?>
    <ul class="neat-requests">
        <?php foreach ($calls as $call): ?>
            <?php
            /**
             * @var $request \Cake\Http\Client\Request
             * @var $response \Cake\Http\Client\Response
             */
            $request  = $call['request'];
            $response = $call['response'];
            ?>
            <li>
                <div onclick="$(this).next('div').toggle();" style="cursor:pointer;">
                    <?= $this->Call->method($request['method']); ?>
                    <?= $this->Call->code($response['status_code']); ?>
                    <?= $request['uri']; ?>
                    <?= $this->Call->time($call['time']); ?>
                </div>
                <div style="display: none;width:100%;">
                    <div class="stack-trace-container">
                        <a href="javascript:;" onclick="$(this).next('div').toggle();" class="button-stacktrace">Toggle stack trace</a>
                        <div style="display: none;">
                            <?= $this->Call->stackTrace($call['trace']); ?>
                        </div>
                    </div>

                    <div class="reqres-container">
                        <?= $this->Call->headers($request['headers'], 'request'); ?>
                        <?= $this->Call->body($request['body'], $request['content-type']); ?>
                    </div>
                    <div class="reqres-container">
                        <?= $this->Call->headers($response['headers'], 'response'); ?>
                        <?= $this->Call->body($response['body'], $response['content-type']); ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <script>
        $(document).ready(function () {
            $('.panel-content pre code:not(.raw)').each(function (i, block) {
                hljs.highlightBlock(block);
            });
        });

        new Clipboard('.panel-content .select-response', {
            target: function (trigger) {
                return $(trigger).siblings('pre').find('code:visible').get(0);
            }
        });
    </script>
<?php endif; ?>
