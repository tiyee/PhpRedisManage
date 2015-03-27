
    	var redisAPP = angular.module('redisAPP', ["ngRoute"], function() { });
    		redisAPP.value('pub',{"db":"999",
    			"checkKey":function(str){
				    			re = /^[a-zA-Z\-_0-9\.\:]+$/;
				   				if(!re.test(str)){
				        			alert("your key is unvalid");
				        			return false;
				    			}else{
				       				 return true;
				   				 }
    			},

    			"getInfo":function($http,db,key,type,$scope) {


    				 $http.get('?db='+db+'&c=redis_'+type+'&a=getInfo&key='+encodeURIComponent(key)).success(function(data) {
    				 	$scope.info = data;
    				 	$scope.info.db = db;
			    		$scope.info.isEdit = false;
			    		$scope.info.isAdd = false;
			    		$scope.info.status = 'show';
			    		$scope.news = {"key":data.key,"ttl":data.ttl};
    					//callback($scope,data);
    				});

    			},
    			"edit":function($scope) {

    				//console.dir($scope);
    				$scope.info.isEdit = true;


    			},
    			"cancel":function($scope) {
	    			$scope.news.key = $scope.info.key;
	    			$scope.info.isEdit = false;
    			},
    			"sub":function($scope,$http,pub,type) {
    			    if(!pub.checkKey($scope.news.key)) {
    			    	return false;
    			    }
    			    if($scope.news.key == $scope.info.key) {
    			    	alert('the new key is the same as the old one !!');
    			    	return false;
    			    }


					$http.post('?db='+pub.db+'&c=redis_'+type+'&a=renameKey', {"oKey":$scope.info.key,"nKey":$scope.news.key})
		            .success(function(data){
		            	alert(data.msg);
		            	if(data.error > 0) {
		            		//return ;
		            	} else {

			                console.info("Saved.");
			               $scope.info.key = $scope.news.key ;
    					   $scope.info.isEdit = false;
    					   location.href = '#/'+type+'/'+encodeURIComponent($scope.info.key);


			            }


		            })


    			},

    			"editTTl":function($scope) {
    				$scope.info.isEditTTL = true;

    			},
    			"subTTL":function($scope,$http,pub,type) {
	    			//console.info(typeof($scope.news.ttl));
	    			if($scope.news.ttl == $scope.info.ttl) {
	    			    	alert('the new ttl is the same as the old ttl !!');
	    			    	return false;
	    			}


						$http.post('?db='+pub.db+'&c=redis_'+type+'&a=setTimeout', {"key":$scope.info.key,"ttl":$scope.news.ttl})
			            .success(function(data){

			            	alert(data.msg);

			            	if(data.error > 0) {
			            		return ;
			            	} else {
			            		$scope.info.isEditTTL = false;
			            		$scope.info.ttl = $scope.news.ttl;
				                console.info("Saved.");

				            }


			            })

    			},
    			"delete":function(key,$http,pub,type) {
	  				$http.get('?db='+pub.db+'c=redis_'+type+'&a=deleteKey&key='+encodeURIComponent(key)).success(function(data) {
			    		alert(data.msg);
			    		if(data.error == 0) {
			    			location.href = '#/index';
			    		}


			   		});
    			}

    	});


		  redisAPP.config(
			['$routeProvider', function($routeProvider) {
			  $routeProvider.
			  	   when('/:key', {templateUrl: '/Static/html/index.html',   controller: "index"}).
			  	   /*when('/:type/:key', {templateUrl: '/Static/html/list.html',   controller: "info"}).*/
			  	   when('/string/:key', {templateUrl: '/Static/html/string.html',   controller: "string"}).
			  	   when('/hash/:key', {templateUrl: '/Static/html/hash.html',   controller: "hash"}).
			  	   when('/set/:key', {templateUrl: '/Static/html/set.html',   controller: "set"}).
			  	   when('/zset/:key', {templateUrl: '/Static/html/zset.html',   controller: "zset"}).
			  	    when('/list/:key', {templateUrl: '/Static/html/list.html',   controller: "list"}).
			  	  when('/index', {templateUrl: '/Static/html/index.html',   controller: "index"}).
			     /* when('/list', {templateUrl: './list.html',   controller: "article_list"}).
			      when('/list/:category', {templateUrl: './list.html',   controller: "article_list"}).
			       when('/list/:category/page/:page', {templateUrl: './list.html',   controller: "article_list"}).
			      when('/post/:id', {templateUrl: './post.html', controller: "article_post"}).*/
			      otherwise({redirectTo: '/index'});
			}]
		);
		redisAPP.config(['$httpProvider',function($httpProvider) {

    		$httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
    		 // Override $http service's default transformRequest
		    $httpProvider.defaults.transformRequest = [function(data) {
		        /**
		         * The workhorse; converts an object to x-www-form-urlencoded serialization.
		         * @param {Object} obj
		         * @return {String}
		         */
		        var param = function(obj) {
		            var query = '';
		            var name, value, fullSubName, subName, subValue, innerObj, i;

		            for (name in obj) {
		                value = obj[name];

		                if (value instanceof Array) {
		                    for (i = 0; i < value.length; ++i) {
		                        subValue = value[i];
		                        fullSubName = name + '[' + i + ']';
		                        innerObj = {};
		                        innerObj[fullSubName] = subValue;
		                        query += param(innerObj) + '&';
		                    }
		                } else if (value instanceof Object) {
		                    for (subName in value) {
		                        subValue = value[subName];
		                        fullSubName = name + '[' + subName + ']';
		                        innerObj = {};
		                        innerObj[fullSubName] = subValue;
		                        query += param(innerObj) + '&';
		                    }
		                } else if (value !== undefined && value !== null) {
		                    query += encodeURIComponent(name) + '='
		                            + encodeURIComponent(value) + '&';
		                }
		            }

		            return query.length ? query.substr(0, query.length - 1) : query;
		        };

		        return angular.isObject(data) && String(data) !== '[object File]'
		                ? param(data)
		                : data;
		    }];


		}]);


		redisAPP.controller('key_list',['$scope','$http','pub',function($scope,$http,pub) {
			$scope.info = {};
			$scope.info.db = 0;
			if(!'number' == typeof($scope.info.db)) {
				alert('db is error');
				return;
			}
			$scope.getKeys = function() {
				pub.db = $scope.info.db;
				$http.get('?db='+$scope.info.db+'&a=json_keyLimit').success(function(data) {
					//console.dir(data);
		    		$scope.info.it = data.it;
		    		$scope.info.keys = data.values;

		    	});
			}
			$scope.location = function(it) {
				if(it == 0) {
    				alert('in the end');
    				return false;
    			}
    			$http.get('?db='+$scope.info.db+'&it='+it+'&a=json_keyLimit').success(function(data) {
					//console.dir(data);
		    		$scope.info.it = data.it;
		    		$scope.info.keys = data.values;

		    	});
			}

			$scope.search = function(it,query) {
    			if(query == '') {
    				alert('the keyword is empty');
    				return false;
    			}
    			alert(query);
    			return false;
    			var data = {
    				"key":$scope.info.key,
    				"keyword":query,
    				"it":it
    			};
    			$http.post('?db='+pub.db+'&c=redis_'+type+'&a=search&key='+$scope.info.key, data)
		            .success(function(data){

		            	//alert(data.msg);
		            	//return false;
		            	if(data.error > 0) {
		            		return ;
		            	} else {
		            		$scope.info = data;
		            		$scope.info.query = data.pattern;

			            }


		        })

    		}





		}]);
		redisAPP.controller('index',function($scope,$http) {

		});
		redisAPP.controller('string',["$scope","$http","pub","$routeParams",function($scope,$http,pub,$routeParams) {

			if('undefined' == typeof($routeParams.key)  ) {
				var key = unkown;
			} else {
				var key = $routeParams.key ;
			}
			var db = pub.db;
			var type = 'string';


			 pub.getInfo($http,db,key,type,$scope);
			$scope.edit = function() {
			 	pub.edit($scope);
			 }
			 $scope.cancel = function() {
			 	pub.cancel($scope);
			 }
			 $scope.sub = function() {
			 	pub.sub($scope,$http,pub,type);
			 }
			 $scope.editTTl = function() {
			 	pub.editTTl($scope);
			 }
			 $scope.subTTL = function() {
			 	pub.subTTL($scope,$http,pub,type);
			 }
			 $scope.delete = function(key) {
			 	pub.delete(key,$http,pub,type);
			 }
			 $scope.editValue = function(index) {

    			$scope.info.nValues = $scope.info.values;
    			$scope.info.isEditValue =!$scope.info.isEditValue;
    		}
    		$scope.subValue = function() {

    			//console.info($scope.info.values[index].isEditValue);
    			if($scope.info.values == $scope.info.nValues) {
    				alert('unchenged !!');
    				return false;
    			}
    			var data = {
    				"key":$scope.info.key,

    				"value":$scope.info.nValues
    			};
    			$http.post('?db='+pub.db+'&c=redis_'+type+'&a=set', data)
		            .success(function(data){


		            	alert(data.msg);

		            	if(data.error > 0) {
		            		return ;
		            	} else {
		            		//$scope.info.isEditTTL = false;
			                console.info("Saved.");
			            	$scope.info.values = $scope.info.nValues;
			            	$scope.info.isEditValue =!$scope.info.isEditValue;
			            	return ;
			            }


		            })


    		}
    		$scope.cancelValue = function(index) {
    			//alert(index);

    			$scope.info.nValues = $scope.info.values;

    			$scope.info.isEditValue =!$scope.info.isEditValue;
    		}
		}]);
		redisAPP.controller('set',["$scope","$http","pub","$routeParams",function($scope,$http,pub,$routeParams) {

			if('undefined' == typeof($routeParams.key)  ) {
				var key = unkown;
			} else {
				var key = $routeParams.key ;
			}
			var db = pub.db;
			var type = 'set';
  			pub.getInfo($http,db,key,type,$scope);

			$scope.edit = function() {
			 	pub.edit($scope);
			 }
			 $scope.cancel = function() {
			 	pub.cancel($scope);
			 }
			 $scope.sub = function() {
			 	pub.sub($scope,$http,pub,type);
			 }
			 $scope.editTTl = function() {
			 	pub.editTTl($scope);
			 }
			 $scope.subTTL = function() {
			 	pub.subTTL($scope,$http,pub,type);
			 }
			 $scope.delete = function(key) {
			 	pub.delete(key,$http,pub,type);
			 }
			 $scope.editValue = function(index) {
			 	$scope.info.values[index].isEdit = false;
			 }
			 $scope.subValue = function(index) {
			 	if($scope.info.values[index].value == $scope.info.values[index].nValue) {
    				alert('unchenged !!');
    				return false;
    			}
    			var data = {
    				"key":$scope.info.key,
    				"nValue":$scope.info.values[index].nValue,
    				"value":$scope.info.values[index].value
    			};
    			$http.post('?db='+pub.db+'&c=redis_'+type+'&a=sReset', data)
		            .success(function(data){

		            	alert(data.msg);

		            	if(data.error > 0) {

		            	} else {
		            		//$scope.info.isEditTTL = false;
			                console.info("seted.");
			            	$scope.info.values[index].value = $scope.info.values[index].nValue;
			            	$scope.info.values[index].isEdit =!$scope.info.values[index].isEdit;

			            }



		            })

			 }
			 $scope.cancelValue = function(index) {
			 	$scope.info.values[index].nValue = $scope.info.values[index].value;
			 	$scope.info.values[index].isEdit = true;
			 }
			 $scope.deleteValue = function(index) {

    			var data = {
    				"key":$scope.info.key,
    				"value":$scope.info.values[index].value,
    			};
    			$http.post('?db='+pub.db+'&c=redis_'+type+'&a=sRem', data)
		            .success(function(data){

		            	alert(data.msg);

		            	if(data.error > 0) {
		            		return ;
		            	} else {
		            		$scope.info.isEdit = false;
			                console.info("hRem.");
			            	$scope.info.values[index].value = $scope.info.values[index].nValue;
			            	$scope.info.values.splice(index,1);

			            }


		            })

    		}
    		$scope.location = function(it) {
    			if(it == 0) {
    				alert('in the end');
    				return false;
    			}
    			var data = {
    				"key":$scope.info.key,
    				"it":it,
    				"keyword":$scope.info.query,
    			};
    			$http.post('?db='+pub.db+'&c=redis_'+type+'&a=getInfo&key='+$scope.info.key, data)
		            .success(function(data){

		            	//alert(data.msg);
		            	//return false;
		            	if(data.error > 0) {
		            		return ;
		            	} else {
		            		$scope.info = data;
		            		$scope.info.query = data.pattern;

			            }


		        })


    		}
    		$scope.search = function(it,query) {
    			if(query == '') {
    				alert('the keyword is empty');
    				return false;
    			}
    			var data = {
    				"key":$scope.info.key,
    				"keyword":query,
    				"it":it
    			};
    			$http.post('?db='+pub.db+'&c=redis_'+type+'&a=search&key='+$scope.info.key, data)
		            .success(function(data){

		            	//alert(data.msg);
		            	//return false;
		            	if(data.error > 0) {
		            		return ;
		            	} else {
		            		$scope.info = data;
		            		$scope.info.query = data.pattern;

			            }


		        })

    		}

		}]);
		redisAPP.controller('list',["$scope","$http","pub","$routeParams",function($scope,$http,pub,$routeParams) {

			if('undefined' == typeof($routeParams.key)  ) {
				var key = unkown;
			} else {
				var key = $routeParams.key ;
			}
			var db = pub.db;
			var type = 'list';


			 pub.getInfo($http,db,key,type,$scope);


			 $scope.edit = function() {
			 	pub.edit($scope);
			 }
			 $scope.cancel = function() {
			 	pub.cancel($scope);
			 }
			 $scope.sub = function() {
			 	pub.sub($scope,$http,pub,type);
			 }
			 $scope.editTTl = function() {
			 	pub.editTTl($scope);
			 }
			 $scope.subTTL = function() {
			 	pub.subTTL($scope,$http,pub,type);
			 }
			 $scope.delete = function(key) {
			 	pub.delete(key,$http,pub,type);
			 }
			 $scope.location = function() {

			 	if($scope.info.pages < $scope.info.page) {
			 		alert('less than '+$scope.info.pages);
			 		return false;
			 	}
			 	var page = $scope.info.page;
			 	$http.get('?db='+db+'&c=redis_'+type+'&a=getLimit&p='+page+'&key='+encodeURIComponent(key)).success(function(data) {
    				 	$scope.info = data;

			    		$scope.info.isEdit = false;
			    		$scope.info.isAdd = false;
			    		$scope.info.status = 'show';
			    		$scope.news = {"key":data.key,"ttl":data.ttl};
    					//callback($scope,data);
    			});
			 }
			 $scope.editValue = function(index) {
			 	$scope.info.values[index].isEdit = false;
			 }
			 $scope.subValue = function(index) {
			 	if($scope.info.values[index].value == $scope.info.values[index].nValue) {
    				alert('unchenged !!');
    				return false;
    			}
    			var data = {
    				"key":$scope.info.key,
    				"index":($scope.info.current-1)*$scope.info.limit+ index,
    				"value":$scope.info.values[index].nValue
    			};
    			$http.post('?db='+pub.db+'&c=redis_'+type+'&a=lSet', data)
		            .success(function(data){

		            	alert(data.msg);

		            	if(data.error > 0) {

		            	} else {
		            		//$scope.info.isEditTTL = false;
			                console.info("seted.");
			            	$scope.info.values[index].value = $scope.info.values[index].nValue;
			            	$scope.info.values[index].isEdit =!$scope.info.values[index].isEdit;

			            }



		            })

			 }
			 $scope.cancelValue = function(index) {
			 	$scope.info.values[index].nValue = $scope.info.values[index].value;
			 	$scope.info.values[index].isEdit = true;
			 }
			 $scope.deleteValue = function(index) {

    			var data = {
    				"key":$scope.info.key,
    				"index":($scope.info.current-1)*$scope.info.limit+ index
    			};
    			$http.post('?db='+pub.db+'&c=redis_'+type+'&a=lRem', data)
		            .success(function(data){

		            	alert(data.msg);

		            	if(data.error > 0) {
		            		return ;
		            	} else {
		            		$scope.info.isEdit = false;
			                console.info("hRem.");
			            	$scope.info.values[index].value = $scope.info.values[index].nValue;
			            	$scope.info.values.splice(index,1);

			            }


		            })

    		}





		}]);
		redisAPP.controller('zset',["$scope","$http","pub","$routeParams",function($scope,$http,pub,$routeParams) {

			if('undefined' == typeof($routeParams.key)  ) {
				var key = unkown;
			} else {
				var key = $routeParams.key ;
			}
			var db = pub.db;
			var type = 'zset';
  			pub.getInfo($http,db,key,type,$scope);
			$scope.edit = function() {
			 	pub.edit($scope);
			 }
			 $scope.cancel = function() {
			 	pub.cancel($scope);
			 }
			 $scope.sub = function() {
			 	pub.sub($scope,$http,pub,type);
			 }
			 $scope.editTTl = function() {
			 	pub.editTTl($scope);
			 }
			 $scope.subTTL = function() {
			 	pub.subTTL($scope,$http,pub,type);
			 }
			 $scope.delete = function(key) {
			 	pub.delete(key,$http,pub,type);
			 }
			 $scope.location = function(it) {
    			if(it == 0) {
    				alert('in the end');
    				return false;
    			}
    			var data = {
    				"key":$scope.info.key,
    				"it":it,
    				"keyword":$scope.info.query,
    			};
    			$http.post('?db='+pub.db+'&c=redis_'+type+'&a=getInfo&key='+$scope.info.key, data)
		            .success(function(data){

		            	//alert(data.msg);
		            	//return false;
		            	if(data.error > 0) {
		            		return ;
		            	} else {
		            		$scope.info = data;
		            		$scope.info.query = data.pattern;

			            }


		        })


    		}
    		$scope.search = function(it,query) {
    			if(query == '') {
    				alert('the keyword is empty');
    				return false;
    			}
    			var data = {
    				"key":$scope.info.key,
    				"keyword":query,
    				"it":it
    			};
    			$http.post('?db='+pub.db+'&c=redis_'+type+'&a=search&key='+$scope.info.key, data)
		            .success(function(data){

		            	//alert(data.msg);
		            	//return false;
		            	if(data.error > 0) {
		            		return ;
		            	} else {
		            		$scope.info = data;
		            		$scope.info.query = data.pattern;

			            }


		        })

    		}
			 $scope.editValue = function(index) {
			 	$scope.info.values[index].isEdit = false;
			 }
			 $scope.subValue = function(index) {
			 	if($scope.info.values[index].value == $scope.info.values[index].nValue && $scope.info.values[index].score == $scope.info.values[index].nScore ) {
    				alert('unchenged !!');
    				return false;
    			}
    			var data = {
    				"key":$scope.info.key,
    				"score":$scope.info.values[index].nScore,
    				"value":$scope.info.values[index].value,
    				"nValue":$scope.info.values[index].nValue
    			};
    			$http.post('?db='+pub.db+'&c=redis_'+type+'&a=zSet', data)
		            .success(function(data){

		            	alert(data.msg);

		            	if(data.error > 0) {

		            	} else {
		            		//$scope.info.isEditTTL = false;
			                console.info("seted.");
			            	$scope.info.values[index].value = $scope.info.values[index].nValue;
			            	$scope.info.values[index].score = $scope.info.values[index].nScore;
			            	$scope.info.values[index].isEdit =!$scope.info.values[index].isEdit;

			            }



		            })

			 }
			 $scope.cancelValue = function(index) {
			 	$scope.info.values[index].nValue = $scope.info.values[index].value;
			 	$scope.info.values[index].nScore = $scope.info.values[index].score;
			 	$scope.info.values[index].isEdit = true;
			 }
			 $scope.deleteValue = function(index) {

    			var data = {
    				"key":$scope.info.key,

    				"value":$scope.info.values[index].value,

    			};
    			$http.post('?db='+pub.db+'&c=redis_'+type+'&a=zRem', data)
		            .success(function(data){

		            	alert(data.msg);

		            	if(data.error > 0) {
		            		return ;
		            	} else {
		            		$scope.info.isEdit = false;
			                console.info("hRem.");
			            	$scope.info.values[index].value = $scope.info.values[index].nValue;
			            	$scope.info.values.splice(index,1);

			            }


		            })

    		}
		}]);
		redisAPP.controller('hash',['$scope','$http','$routeParams','pub',function($scope,$http,$routeParams,pub) {

			if('undefined' == typeof($routeParams.key)  ) {
				var key = unkown;
			} else {
				var key = $routeParams.key ;
			}
			var db = pub.db;
			var type = 'hash';
			pub.getInfo($http,db,key,type,$scope);
			$scope.edit = function() {
			 	pub.edit($scope);
			 }
			 $scope.cancel = function() {
			 	pub.cancel($scope);
			 }
			 $scope.sub = function() {
			 	pub.sub($scope,$http,pub,type);
			 }
			 $scope.editTTl = function() {
			 	pub.editTTl($scope);
			 }
			 $scope.subTTL = function() {
			 	pub.subTTL($scope,$http,pub,type);
			 }
			 $scope.delete = function(key) {
			 	pub.delete(key,$http,pub,type);
			 }

    		$scope.editValue = function(index) {

    			//console.info($scope.info.values[index].isEditValue);
    			$scope.info.values[index].isEditValue =!$scope.info.values[index].isEditValue;
    		}
    		$scope.subValue = function(index) {

    			//console.info($scope.info.values[index].isEditValue);
    			if($scope.info.values[index].value == $scope.info.values[index].nValue) {
    				alert('unchenged !!');
    				return false;
    			}
    			var data = {
    				"key":$scope.info.key,
    				"field":$scope.info.values[index].field,
    				"value":$scope.info.values[index].nValue
    			};
    			$http.post('?db='+pub.db+'&c=redis_hash&a=hSet', data)
		            .success(function(data){

		            	alert(data.msg);

		            	if(data.error > 0) {
		            		return ;
		            	} else {
		            		//$scope.info.isEditTTL = false;
			                console.info("Saved.");
			            	$scope.info.values[index].value = $scope.info.values[index].nValue;
			            	$scope.info.values[index].isEditValue =!$scope.info.values[index].isEditValue;
			            	return ;
			            }


		            })


    		}
    		$scope.cancelValue = function(index) {
    			//alert(index);

    			//console.info($scope.info.values[index].isEditValue);
    			$scope.info.values[index].nValue =  $scope.info.values[index].value;
    			$scope.info.values[index].isEditValue =!$scope.info.values[index].isEditValue;
    		}
    		$scope.deleteValue = function(field,index) {
    			if(!confirm('are you sure that delete this field:'+field+' ?')) {

    				return false;
    			}
    			var data = {
    				"key":$scope.info.key,
    				"field":field
    			};
    			$http.post('?db='+pub.db+'&c=redis_hash&a=hDel', data)
		            .success(function(data){

		            	alert(data.msg);

		            	if(data.error > 0) {
		            		return ;
		            	} else {
		            		$scope.info.isEditTTL = false;
			                console.info("hDel.");
			            	$scope.info.values[index].value = $scope.info.values[index].nValue;
			            	$scope.info.values.splice(index,1);
			            	return ;
			            }


		            })

    		}
    		$scope.addField = function() {
    			$scope.info.isAdd = true;
    			$scope.info.add = {"field":"","value":""};

    		}
    		$scope.subField= function() {
    			var data = $scope.info.add;
    			data.key = $scope.info.key;
    			$http.post('?db='+pub.db+'&c=redis_hash&a=hSetNx', data)
		            .success(function(data){

		            	alert(data.msg);

		            	if(data.error > 0) {
		            		//return false ;
		            	} else {
		            		$scope.info.isAdd = false;
			                //console.info("hSetNx.");



			            	$scope.info.values.unshift($scope.info.add);
			            	//$scope.info.add = {};
			            	return false ;
			            }


		            })


    		}
    		$scope.cancelField = function() {
    			$scope.info.isAdd = false;
    		}



		}]);

		redisAPP.controller('info',function($scope,$http,$routeParams) {
			if('undefined' == typeof($routeParams.type)  ) {
				var type = 'unkown';
			} else {
				var type = $routeParams.type ;
			}
			if('undefined' == typeof($routeParams.key)  ) {
				var key = unkown;
			} else {
				var key = $routeParams.key ;
			}
  			$http.get('?c=process&a=redis_'+type+'&key='+encodeURIComponent(key)).success(function(data) {
		    		$scope.info = data;
		  });
		});
		redisAPP.controller('article_list',function($scope,$http,$routeParams) {
			if('undefined' == typeof($routeParams.category)  ) {
				var category = 0;
			} else {
				var category = $routeParams.category ;
			}
			//alert(typeof());
		  	$http.get('http://www.ci.com:8888/?c=list&a=test&category='+category).success(function(data) {
		    /*$scope.article_list = data;*/
		    $scope.article_info = new Array();
		    data.forEach(function(e){
		    	$http.get('http://www.ci.com:8888/?c=post&a=info&id='+e).success(function(data2) {
		    		$scope.article_info.push(data2);
		 		 });
		    })
		    $scope.article_info.sort();
		 });
		});

		redisAPP.controller('setting',function($scope,$http) {
  			 $http.get('http://www.ci.com:8888/?c=setting&a=test').success(function(data) {
		    		$scope.setting = data;
		  });
		});


		redisAPP.controller('article_post',function($scope,$http,$routeParams) {
			if('undefined' == typeof($routeParams.id)  ) {
				var post = 0;
			} else {
				var post = $routeParams.id ;
			}
  			$http.get('http://www.ci.com:8888/?c=post&a=info&id='+post).success(function(data) {
		    		$scope.info = data;
		  });
		});





