<span ng-if="(!noneText || count)"><span ng-if='!textOnly'>@{{ count }}</span> <ng-pluralize count="count" when="{
        'one': when[type][0],
        'few': when[type][1],
        'many': when[type][2]
    }"></ng-pluralize></span>
<span ng-if='noneText && !count'>@{{ noneText }}</span>