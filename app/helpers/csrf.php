<?php
function csrf_token() {
    return \CsrfMiddleware::generate();
}
