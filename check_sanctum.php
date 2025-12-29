<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Checking User model...\n";
    $user = new \App\Models\User();
    
    if (method_exists($user, 'createToken')) {
        echo "Method createToken EXISTS on User model.\n";
    } else {
        echo "Method createToken DOES NOT exist on User model.\n";
        
        // Inspect traits
        echo "Traits used by User:\n";
        $traits = class_uses(\App\Models\User::class);
        print_r($traits);
        
        // Check if HasApiTokens trait has the method
        if (in_array('Laravel\Sanctum\HasApiTokens', $traits)) {
             echo "User uses Laravel\Sanctum\HasApiTokens.\n";
             $traitMethods = get_class_methods('Laravel\Sanctum\HasApiTokens');
             // Since it's a trait, we can't inspect it directly with get_class_methods like a class easily if not instantiated, 
             // but checking if the trait is used is a good start.
             
             // Reflection
             $reflector = new ReflectionClass('Laravel\Sanctum\HasApiTokens');
             if ($reflector->hasMethod('createToken')) {
                 echo "Trait HasApiTokens HAS createToken method.\n";
             } else {
                 echo "Trait HasApiTokens DOES NOT HAVE createToken method.\n";
             }
        }
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
