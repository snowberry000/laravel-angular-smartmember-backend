<?php

return [
	// Vimeo Integration
	"vimeo" => array(
		"client_id" => "b9b2d0ab892acdfa7f6580d9eec5eca1a1a15655",
		"client_secret" => "hN3EHlvzy9WdCaHEHCwT9HlX8Fx7vfnOIfOSx1pmuWiQFz1x0zMl3tszlQIjpEITUE6mNL1tb0+Pv5RPvRJlauIyozHmwjiZY/4OAt6cqY86hpvYmb6huXJ+GN1r3dmq"
	),

	// Stripe integration: Update productio nsettings
	"stripe" => array(
		"client_id" => "ca_6V222PzuCejTll1T8Jey9GlVah7cmFta",
		"secret_key" => "sk_live_BgLKVDUh9wlTaVJfyGrpkGah",
		"public_key" => "pk_live_bpyPe50gAVEmDDDxZ7brA0HN"
	),
	
	"sendgrid" => array(
		"api_user" => env('SENDGRID_USERNAME'),
		"api_pass" => env('SENDGRID_PASSWORD'),
	),
];