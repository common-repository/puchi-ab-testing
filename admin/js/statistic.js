(function($){
        'use strict'
        window.puchi = {
                el : {
                        window : $(window),
                        document : $(document)
                },
		def: {
			chartColor : {
				0:{
					color: "rgba(33, 150, 243, 1)",
					oColor: "rgba(33, 150, 243, 0.5)",
				},
				1:{
					color: "rgba(82, 185, 71, 1)",
					oColor: "rgba(82, 185, 71, 0.5)",
				},
				2:{
					color: "rgba(239, 83, 80, 1)",
					oColor: "rgba(239, 83, 80, 0.5)"
				},
				3:{
					color: "rgba(121, 85, 72, 1)",
					oColor: "rgba(121, 85, 72, 0.5)"
				},
				4:{
					color: "rgba(103, 58, 183, 1)",
					oColor: "rgba(103, 58, 183, 0.5)"
				}
			},
			pchChart: '',
			pchChartData: ''
		},
		fn : {
			puchiConfirm: function(data){
				$.confirm({
					title: data.title,
					animateFromElement: false,
					content: data.content,
					buttons:  {
						confirm:  {
							btnClass: 'button button-primary',
							action: data.action
						},
						cancel: {
							btnClass: 'button',
						},
					},
					backgroundDismiss : true
				});
			},
			puchiAlert: function(data){
				$.alert({
					title: data.title,
					content: data.content,
					animateFromElement: false,
					buttons: {close: {btnClass: 'btn-hidden',action: function(){}}},
					autoClose: "close|100",
					backgroundDismiss : true
				});	
			},
			puchiGenerateTbl: function(){
				$('.pch-tbl').each(function(){
					var data = $(this).data('table'),
						args = {
							pagingType: 'full_numbers',
							language: {
								paginate: {
									last: '&#187;', 
									first: '&#171;' ,
									next: '&#155;', 
									previous: '&#139;' 
								}
						      },
						      order: [[ 1, "desc" ]]
						};
						if( typeof data !== 'undefined' && typeof data === 'object' && data !== null  ){
							for (var prop in data) {
								if (data.hasOwnProperty(prop)) {
									args[prop] = data[prop];
								}
							}
						}
					$(this).find('table').DataTable(args);
					$('<div class="clear"></div>').insertBefore($(this).find('table'));
					$('<div class="clear"></div>').appendTo($(this).find(' > *:first-child'));
				});
			},
			puchiFetchTable: function(data, target){
				$.post( puchi_data.api_url + "get_range_statistic_data/" ,{
					type : data.type,
					page : data.page,
					split : data.split,
					content: data.content
				}, function(result){
					target.html(result.content);
					target.removeClass('fetching');
				});
			},
			puchiDrawChart: function(data){
				const labels = data.label;
				var datasets = [],
					n = 0;
				
				$.each(data.split, function(i, v){
					datasets.push({
						label: v.label,
						data: v.data[data.type],
						borderColor: puchi.def.chartColor[n].color,
						backgroundColor: puchi.def.chartColor[n].oColor
					});
					n++;
				});
				
				var localConfig = {
					type: 'line',
					data: {
						labels: labels,
						datasets: datasets
					},
					options: {
						responsive: true,
						legend: {
							display: true,
							position: 'bottom'
						},
						hover: {
							mode: 'index'
						},
						tooltips: {}
					}
				};
				if (puchi.def.pchChart != '') {
					puchi.def.pchChart.destroy();
				}
				
				setTimeout(function() {
					var ctx = document.getElementById('pch-chart').getContext('2d');
					puchi.def.pchChart =  new Chart(ctx, localConfig);
					$('.pch-chart-widget .pch-widget-body').removeClass('fetching');
				}, 100);
			},
			puchiFetchChartData: function(page, split, range, custom, type){
				$.post( puchi_data.api_url + "get_chart_statistic_data/" ,{
					page : page,
					split : split,
					range: range,
					custom: custom
				}, function(result){
					if (result.status == 'ok') {
						puchi.def.pchChartData = result;
						puchi.def.pchChartData.type = type;
						puchi.fn.puchiDrawChart(puchi.def.pchChartData);
					}else{
						if (puchi.def.pchChart != '') {
							puchi.def.pchChart.destroy();
						}
						$('.pch-chart-widget .pch-widget-body').removeClass('fetching');
						puchi.def.pchChartData = '';
					}
				});
			},
			puchiFetchTableData: function(page, split, range, custom) {
				$.post( puchi_data.api_url + "get_table_statistic_data/", {
					page: page,
					split: split,
					range: range,
					custom: custom
				}, function(data){
					if (data.status == 'ok') {
						var table = $("#pch-stat-content-tbl table");
						if (table.hasClass('dataTable')) {
							table.dataTable().fnDestroy();
						}
						$('#pch-stat-content-tbl table tbody').html(data.content);
						puchi.fn.puchiGenerateTbl();
					}
				});
			}
		},
                run: function(){
			
                        //WINDOW LOAD
                        puchi.el.window.load(function(){
				if ($('#pch-chart').length) {
					// Init datepicker
					$(".pch-datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
					var from = $('input[name="from"].pch-datepicker').first(),
						to = $('input[name="to"].pch-datepicker').first();
						
					from.on('change', function() {
						var start_date = $(this).datepicker('getDate');
						to.datepicker("option", "disabled", false) ;
						start_date.setDate(start_date.getDate() + 1);
						to.datepicker('option', 'minDate', start_date);
					});
					
					to.datepicker("option", "minDate", new Date()) ;
					to.datepicker("option", "maxDate", new Date()) ;
					to.datepicker("option", "disabled", true) ;
					from.datepicker("option", "maxDate", new Date()) ;
					
					$('#ui-datepicker-div *').remove();
					
					var chartConfig = $('.pch-chart-widget').data('chart'),
						page = chartConfig.page,
						split = chartConfig.split,
						range = chartConfig.range,
						custom = [],
						type = chartConfig.type;
						
					puchi.fn.puchiFetchChartData(page, split, range, custom, type);
					
					$('.pch-chart-type .dropholder a' ).click(function(e){
						e.preventDefault();
						if (!$(this).closest('li').hasClass('active')) {
							var self = $(this),
								parent = self.closest('.pch-chart-widget'),
								title = parent.find('.pch-widget-head').find('h2');
								
							parent.find('.pch-chart-type > span b').html(self.text());
							$('.pch-chart-type .dropholder li').removeClass('active');
							self.closest('li').addClass('active');
							title.html(self.text());
							type = self.attr('data-stat');
							puchi.def.pchChartData.type = self.attr('data-stat');
							puchi.fn.puchiDrawChart(puchi.def.pchChartData);
						}
					});
					
					$('.pch-chart-range .dropholder a' ).click(function(e){
						e.preventDefault();
						if (!$(this).closest('li').hasClass('active')) {
							var self = $(this),
								parent = self.closest('.pch-range-select');
								
							if (!parent.hasClass('fetching')) {
								range = self.attr('data-range');
								if (range != 'custom') {
									parent.find('.custom-range').removeClass('displayed');
									parent.addClass('fetching');
									puchi.fn.puchiFetchChartData(page, split, range, custom, type);
									puchi.fn.puchiFetchTableData(page, split, range, custom);
									parent.removeClass('fetching');
								}else{
									parent.find('.custom-range').addClass('displayed');
									if (custom.length > 1) {
										parent.addClass('fetching');
										puchi.fn.puchiFetchChartData(page, split, range, custom, type);
										puchi.fn.puchiFetchTableData(page, split, range, custom);
										parent.removeClass('fetching');
									}
								}
								$('.pch-chart-range .dropholder li').removeClass('active');
								parent.find('.pch-chart-range > span b').html(self.text());
								self.closest('li').addClass('active');
							}
						}
					});
					
					$('.pch-range-select .custom-range input[name="from"]').change(function() {
						var from = $(this).val();
						custom[0] = from;
						if (custom.length > 1) {
							puchi.fn.puchiFetchChartData(page, split, range, custom, type);
							puchi.fn.puchiFetchTableData(page, split, range, custom);
						}
					});
					
					$('.pch-range-select .custom-range input[name="to"]').change(function() {
						var to = $(this).val();
						
						custom[1] = to;
						if (custom.length > 1) {
							puchi.fn.puchiFetchChartData(page, split, range, custom, type);
							puchi.fn.puchiFetchTableData(page, split, range, custom);
						}
					});
					
					if ($('#pch-stat-content-tbl').length) {
						puchi.fn.puchiFetchTableData(page, split, range, custom);
					}
				}
                        });
                        
                        //WINDOW READY
                        puchi.el.document.ready(function(){
				if ($('#pch-stat-page-tbl').length) {
					$.get( puchi_data.api_url + "get_page_statistic_data/", function(data){
						$('#pch-stat-page-tbl table tbody').html(data.content);
						if (data.status == 'ok') {
							puchi.fn.puchiGenerateTbl();
						}
					});
					
					$(document).on('click','.pch-tbl .action .delete', function(e){
						e.preventDefault();
						
						var self = $(this),
							target = self.closest('tr'),
							title = target.find('td:first-child a').text(),
							table = self.closest('table'),
							data = {
								'title' : puchi_data.lang.confirm,
								'content' : puchi_data.lang.s1 + " <b>"+ title+"</b> " + puchi_data.lang.s2,
								'action' : function(){ 
									target.fadeOut('fast', function(){
										$.post( puchi_data.api_url + "delete_page_statistic_data/" ,{ id: target.data('id') } );
										table.dataTable().fnDestroy();
										target.remove();
										puchi.fn.puchiGenerateTbl();
									});
								}
							};
						puchi.fn.puchiConfirm(data);
					});
				}
				
				//STATISTIC TABLE
				if ($('.pch-range-widget').length) {
					var parent = $('.pch-range-widget'),
						target = parent.find('.pch-widget-body'),
						data = parent.data('table');
							
					setTimeout(function(){
						puchi.fn.puchiFetchTable(data, target);
					}, 1);
					
					$('.pch-stat-type .dropholder a').click(function(e){
						e.preventDefault();
						var self = $(this),
							title = self.closest('.pch-widget-head').find('h2'),
							stat = self.attr('data-stat'),
							parent = self.closest('.pch-dropselect');
							
						if (!target.hasClass('fetching')) {
							target.addClass('fetching');
							parent.find('.dropholder li').removeClass('active');
							parent.find('> span b').html(self.text());			
							self.closest('li').addClass('active');
							title.html(self.text());
							data.type = stat;
							puchi.fn.puchiFetchTable(data, target);
						}
					});
					
					$('.pch-split-content .dropholder a').click(function(e){
						e.preventDefault();
						var self = $(this),
							content = self.attr('data-split-content'),
							parent = self.closest('.pch-dropselect');
							
						if (!target.hasClass('fetching')) {
							target.addClass('fetching');
							parent.find('.dropholder li').removeClass('active');
							parent.find('> span b').html(self.text());			
							self.closest('li').addClass('active');
							data.content = content;
							puchi.fn.puchiFetchTable(data, target);
						}
					});
				}
				
				if ($('.pch-dropselect .dropholder .split-holder ul').length) {
					$(".pch-dropselect .dropholder .split-holder ul").scrollBox();
				}
				
				$(".pch-dropselect .dropholder .split-holder input").on("keyup", function(){
					var self = $(this),
						targetParent = self.closest('.split-holder').find('ul'),
						value = self.val(),
						close = self.closest('.split-holder').find('i');
					
					if (value !='') {
						targetParent.find('li').addClass('hidden');
						close.removeClass('hidden');
						targetParent.find('li[data-filter*="' + value.toLowerCase() + '"]').removeClass('hidden');
					}else{
						targetParent.find('li').removeClass('hidden');
						close.addClass('hidden');
					}
				});
				
				$(".pch-dropselect .dropholder .split-holder i").click(function(){
					var self = $(this),
						input = self.prev('input');
						
					input.val('');
					self.addClass('hidden');
					input.trigger('keyup');
				});
				
				$('.pch-setting-item').each(function(){
					var parent = $(this),
						self = parent.find('.btn-save'),
						target = parent.find('.pch-to-save');
					
					self.click(function(e){
						e.preventDefault();
						
						if (target.val() != '') {
							$.post( puchi_data.api_url + "set_settings/" ,{
								key : target.attr('data-key'),
								value: target.val()
							}, function(result){
								puchi.fn.puchiAlert(result);
							});
						}
					});
				});
			});
                }
        }
        
        puchi.run();
        
}(jQuery));