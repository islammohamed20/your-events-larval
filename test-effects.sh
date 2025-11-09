#!/bin/bash
# Test Different Transition Effects
# Usage: bash test-effects.sh [effect] [duration]
# Effects: fade, slide, zoom, flip
# Duration: 2000-15000 (milliseconds)

EFFECT=${1:-fade}
DURATION=${2:-6000}

echo "🎬 تغيير التأثير إلى: $EFFECT"
echo "⏱️  المدة: $(($DURATION / 1000)) ثواني"

cd /var/www/your-events

php artisan tinker --execute="
    \$firstSlide = \App\Models\HeroSlide::orderBy('order')->first();
    if (\$firstSlide) {
        \$firstSlide->update([
            'transition_effect' => '$EFFECT',
            'duration' => $DURATION
        ]);
        echo '✅ تم تحديث التأثير بنجاح!' . PHP_EOL;
        echo 'Effect: ' . \$firstSlide->transition_effect . PHP_EOL;
        echo 'Duration: ' . (\$firstSlide->duration / 1000) . ' seconds' . PHP_EOL;
    } else {
        echo '❌ لا توجد سلايدات!' . PHP_EOL;
    }
"

php artisan view:clear > /dev/null 2>&1

echo ""
echo "🌐 الآن افتح المتصفح وحدّث الصفحة لرؤية التأثير!"
echo ""
echo "💡 لتجربة تأثير آخر:"
echo "   bash test-effects.sh fade 6000"
echo "   bash test-effects.sh slide 4000"
echo "   bash test-effects.sh zoom 7000"
echo "   bash test-effects.sh flip 5000"
