  $(function(){
	  	$('.dui').click(function(){
		$('.opacity').css('display','block')
		$('.exchange').css('display','block')
			$(".flipster").flipster({
				itemContainer: 			'ul', // Container for the flippin' items.
				itemSelector: 			'li', // Selector for children of itemContainer to flip
				style:							'coverflow', // Switch between 'coverflow' or 'carousel' display styles
				start: 							'center', // Starting item. Set to 0 to start at the first, 'center' to start in the middle or the index of the item you want to start with.
				
				enableKeyboard: 		true, // Enable left/right arrow navigation
				enableMousewheel: 	true, // Enable scrollwheel navigation (up = left, down = right)
				enableTouch: 				true, // Enable swipe navigation for touch devices
				
				enableNav: 					true, // If true, flipster will insert an unordered list of the slides
				enableNavButtons: 	true, // If true, flipster will insert Previous / Next buttons
				
				onItemSwitch: 			function(){}, // Callback function when items are switches
			});		
	})
	$('.active1').click(function(){
		$('.opacity').css('display','block')
		$('.rule1').css('display','block')
		
	})
	$('.prize').click(function(){
		$('.opacity').css('display','block')
		$('.rule2').css('display','block')
		
	})
	$('.login_close').click(function(){
		$('.opacity').css('display','none')
		$('.success').css('display','none')
		$('.rule2').css('display','none')
		$('.rule1').css('display','none')
		$('.return').css('display','none')
		$('.login').css('display','none')
		$('.my').css('display','none')
		$('.exchange').css('display','none')
		$('.zuqiu').css('display','none')
	})
	$('.return_fen').click(function(){
		$('.opacity').css('display','none')
		$('.please').css('display','none')
		
	})
	$('.friend_now').click(function(){
		$('.opacity').css('display','block')
		$('.friend').css('display','block')
		
	})
  })