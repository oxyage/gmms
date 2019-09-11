/* 
функции кнопок в панели управления
*/
$( function(){ 

	/* действия на кликах меню */
	$(".panel-button").click(function(li){
		
		let data = $(this).data();
		route = data.route.split("/");

		console.log(route);




		
	});





});