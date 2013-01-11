function NoClickDelay(el) {
	this.element = typeof el == 'object' ? el : document.getElementById(el);
	if( window.Touch ) this.element.addEventListener('touchstart', this, false);
}

NoClickDelay.prototype = {
	handleEvent: function(e) {
		switch(e.type) {
			case 'touchstart': this.onTouchStart(e); break;
			case 'touchmove': this.onTouchMove(e); break;
			case 'touchend': this.onTouchEnd(e); break;
		}
	},
	
	onTouchStart: function(e) {
		e.preventDefault();
		this.moved = false;
	
		
		this.theTarget = document.elementFromPoint(e.targetTouches[0].clientX, e.targetTouches[0].clientY);
		if(this.theTarget.nodeType == 3) this.theTarget = theTarget.parentNode;
		
	//	this.theTarget.className+= ' ui-state-active';
		jQuery(this.element).addClass('ui-state-active');
		
	
		this.element.addEventListener('touchmove', this, false);
		this.element.addEventListener('touchend', this, false);
	},
	
	onTouchMove: function(e) {
		this.moved = true;
		
		
		
		
		this.theTarget.className = this.theTarget.className.replace(/ ?pressed/gi, '');
	},
	
	onTouchEnd: function(e) {
		this.element.removeEventListener('touchmove', this, false);
		this.element.removeEventListener('touchend', this, false);
	
		
	
		jQuery(this.element).removeClass('ui-state-active');
	
	
	
	
	
	//	if( !this.moved && this.theTarget ) {
	
		if( this.theTarget ) {

			jQuery(this.element).removeClass('ui-state-active');
						
			var theEvent = document.createEvent('MouseEvents');
			theEvent.initEvent('click', true, true);
			this.theTarget.dispatchEvent(theEvent);
			
		}
	
	
	
	
		this.theTarget = undefined;
	}
}