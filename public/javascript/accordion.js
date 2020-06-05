// ---------------------------------------------------- //
// SIMPLE ACCORDION v1.5
// Last update : November, 2019
// Author : BeliG
// Documentation : http://www.design-fluide.com/?p=1416
// ---------------------------------------------------- //

(function($) {

   $.fn.simpleAccordion = function(options) {

      // Merge user's options with defaults
      var opts = $.extend({
         item: '.accordion-item', // Wrapper of trigger + content
         trigger: '.accordion-trigger', // Item's trigger
         content: '.accordion-content', // Content to show/hide
         active: 'active', // Active class (= opened item)
         autoclose: false, // Close opened items when clicking anywhere on docment (but on the accordion)
         multiple: false, // Allow multiple items opened at once
         speed: 200, // Open/close duration
         debug: false
      }, options);

      // [Function] Check if item is active (= opened)
      var isActive = function($item) {
         return $item.hasClass(opts.active); // hasClass() is slightly faster than is()
      };

      // [Function] Close all active items
      var closeActiveItems = function($items) {
         $items.each(function() {
            var $item = $(this);
            isActive($item) ? close($item) : '';
         });
      };

      // [Function] Close accordion item
      var close = function($item) {
         $item
            .find(opts.content).eq(0) // Only find the first content (in case of nested accordions)
            .slideUp(opts.speed, function() { // Slide up = close
               $item.removeClass(opts.active);
            });
      };

      // [Function] Open accordion item
      var open = function($item) {
         var $content = $item.find(opts.content).eq(0); // Only find the first content (in case of nested accordions)
         $content.hide(); // Force display:none before adding the active class
         $item.addClass(opts.active);
         $content.slideDown(opts.speed); // Slide down = open
      };

      // [Function] Filter nested elements
      var filterNestedElements = function($parent, $elements, selector) {
         return $elements.not(function() { // not() is slightly faster than filter()
            return $(this).is($parent.find(opts.content + ' ' + selector)); // Exclude nested elements (children of opts.content)
         });
      };
      
      // Init accordions
      return this.each(function(){

         // Caching elements
         var $parent = $(this),
             $items = $parent.find(opts.item),
             $triggers = $parent.find(opts.trigger);

         // Filter nested triggers
         $triggers = filterNestedElements($parent, $triggers, opts.trigger);

         // Filter nested items
         $items = filterNestedElements($parent, $items, opts.item);

         // Accordion has items?
         var hasItem = !!$items.length;

         // Debug
         if(opts.debug) {
            console.log('-------------------------------');
            console.log('parent: ', $parent);
            console.log('hasItem: ', hasItem);
            console.log('items: ', $items.length);
            console.log('triggers: ', $triggers.length);
         }
         
         // Triggers handlers
         $triggers
            .off('click.simpleAccordion') // Fix issues related to multiple initializations
            .on('click.simpleAccordion', function(e){
               e.preventDefault();
               var $item = hasItem ? $(this).closest(opts.item) : $parent; // If trigger has no item container: item = parent
               !opts.multiple && hasItem ? closeActiveItems($items.not($item)) : ''; // If not allowing several items opened at once: close active ones
               isActive($item) ? close($item) : open($item); // If item is opened = close, if closed = open
            });

         // Autoclose
         if(opts.autoclose) {
            $(document).on('click', function(e){ // When clicking anywhere...
               if($(e.target).closest($parent).length === 0) { // ... except in the accordion
                  if(hasItem) { closeActiveItems($items); } // Close all opened items if accordion has items
                  else if(isActive($parent)) { close($parent); } // Else just close the parent (if it's opened)
               }
            });
         }

      });
   };

})(jQuery);