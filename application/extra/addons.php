<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'sms_send' => 
    array (
      0 => 'alisms',
    ),
    'sms_notice' => 
    array (
      0 => 'alisms',
    ),
    'sms_check' => 
    array (
      0 => 'alisms',
    ),
    'app_init' => 
    array (
      0 => 'epay',
    ),
    'get_cfg' => 
    array (
      0 => 'litestore',
    ),
  ),
  'route' => 
  array (
    '/example$' => 'example/index/index',
    '/example/d/[:name]' => 'example/demo/index',
    '/example/d1/[:name]' => 'example/demo/demo1',
    '/example/d2/[:name]' => 'example/demo/demo2',
    '/third$' => 'third/index/index',
    '/third/connect/[:platform]' => 'third/index/connect',
    '/third/callback/[:platform]' => 'third/index/callback',
  ),
);