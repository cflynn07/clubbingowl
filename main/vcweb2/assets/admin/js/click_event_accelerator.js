(function(){
	
	var NoClickDelay = function(el) {
		
		console.log('NoClickDelay')
		
		this.element = el;
		if( window.Touch ) this.element.addEventListener('touchstart', this, false);
	}
	
	NoClickDelay.prototype = {
		handleEvent: function(e) {
			
			console.log('hello from NoClickDelay.prototype.handleEvent');
			
			switch(e.type) {
				case 'touchstart': this.onTouchStart(e); break;
				case 'touchmove': this.onTouchMove(e); break;
				case 'touchend': this.onTouchEnd(e); break;
			}
		},
	
		onTouchStart: function(e) {
			e.preventDefault();
			this.moved = false;
			
			
			var el = jQuery(this.element);
			if(el.hasClass('ui-button')){
				
				if(!el.hasClass('ui-state-active'))
					el.addClass('ui-state-active');
				
			}
			
	
			this.element.addEventListener('touchmove', this, false);
			this.element.addEventListener('touchend', this, false);
		},
	
		onTouchMove: function(e) {
			this.moved = true;
		},
	
		onTouchEnd: function(e) {
			this.element.removeEventListener('touchmove', this, false);
			this.element.removeEventListener('touchend', this, false);
	
			if( !this.moved ) {
				// Place your code here or use the click simulation below
				var theTarget = document.elementFromPoint(e.changedTouches[0].clientX, e.changedTouches[0].clientY);
				if(theTarget.nodeType == 3) theTarget = theTarget.parentNode;
	
				var theEvent = document.createEvent('MouseEvents');
				theEvent.initEvent('click', true, true);
				theTarget.dispatchEvent(theEvent);
			}
		}
	};
	
	jQuery.extend({
		NoClickDelay: function(el){
			new NoClickDelay(el);
		}
	});
	
})();
