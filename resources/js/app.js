/*global jQuery, Handlebars, Router */
jQuery(function ($) {
	'use strict';

	Handlebars.registerHelper('eq', function (a, b, options) {
		return a === b ? options.fn(this) : options.inverse(this);
	});

	var ENTER_KEY = 13;
	var ESCAPE_KEY = 27;

	var util = {
		pluralize: function (count, word) {
			return count === 1 ? word : word + 's';
		},
		store: function (namespace, data) {
			if (arguments.length > 1) {
				return localStorage.setItem(namespace, JSON.stringify(data));
			} else {
				var store = localStorage.getItem(namespace);
				return (store && JSON.parse(store)) || [];
			}
		}
	};

	var api = {
		itemUrl: 'http://todo-api.loc/items',
		listUrl: 'http://todo-api.loc/lists',
		authUrl: 'http://todo-api.loc/auth/token',


		setHeaders: function(jwtToken) {
			this.headers = {};
			this.headers.Authorization = 'Bearer ' + jwtToken;
		},

		createList: function(name, callback) {
			$.ajax({
				type: "POST",
				url: this.listUrl,
				headers: this.headers,
				data: {
					name: name
				},
				dataType: "json",
				success: function(listObject) {
					console.log(listObject);
					callback(listObject);
				}
			});
		},

		getJwtToken: function(credentials, callback) {
			$.ajax({
				type: "POST",
				url: this.authUrl,
				data: {
					username: credentials.username,
					password: credentials.password
				},
				dataType: "json",
				success: function(response) {
					console.log(response);
					callback(response.access_token);
				}
			});
		},

		getItems: function (listId, callback) {
			$.getJSON({
				url: this.itemUrl,
				headers: this.headers,
				data: {
					list_id: listId
				},
				success: function (response) {
					console.log(response);
					callback(response.items)
				}
			})
		},

		createItem: function(listId, value, callback) {
			$.ajax({
				type: "POST",
				url: this.itemUrl,
				headers: this.headers,
				data: {
					list_id: listId,
					title: value
				},
				dataType: 'json',
				success: function (response) {
					callback(response)
				}
			});
		},
		destroyItem: function(todo) {
			var url = this.itemUrl + '/' + todo.id;
			$.ajax({
				type: "DELETE",
				url: url,
				headers: this.headers,
				success: function(response) {
					console.log(response);
				}
			});
		},
		updateItem: function(todo) {
			var url = this.itemUrl + '/' + todo.id;
			$.ajax({
				type: "PATCH",
				url: url,
				headers: this.headers,
				data: {
					title: todo.title,
					completed: (todo.completed) ? todo.completed : false
				},
				success: function(response) {
					console.log(response);
				}
			});
		}
	};

	var App = {
		init: function () {
			this.todos = util.store('todos-jquery');
			this.list = util.store('list-jquery');

			this.todoTemplate = Handlebars.compile($('#todo-template').html());
			this.footerTemplate = Handlebars.compile($('#footer-template').html());
			if (this.list.length === 0) {
				// Creating list and get items in callback
				api.createList('List', this.integrateList.bind(this));
			}
			else {
				// get items of created list
				api.getItems(this.list[0].id , this.integrateItems.bind(this));
			}
			this.bindEvents();

			new Router({
				'/:filter': function (filter) {
					this.filter = filter;
					this.render();
				}.bind(this)
			}).init('/all');
		},
		bindEvents: function () {
			$('#new-todo').on('keyup', this.create.bind(this));
			$('#toggle-all').on('change', this.toggleAll.bind(this));
			$('#footer').on('click', '#clear-completed', this.destroyCompleted.bind(this));
			$('#todo-list')
				.on('change', '.toggle', this.toggle.bind(this))
				.on('dblclick', 'label', this.editingMode.bind(this))
				.on('keyup', '.edit', this.editKeyup.bind(this))
				.on('focusout', '.edit', this.update.bind(this))
				.on('click', '.destroy', this.destroy.bind(this));
			//$('#signin').on('click', this.signin.bind(this));
		},
		render: function () {
			var todos = this.getFilteredTodos();
			$('#todo-list').html(this.todoTemplate(todos));
			$('#main').toggle(todos.length > 0);
			$('#toggle-all').prop('checked', this.getActiveTodos().length === 0);
			this.renderFooter();
			$('#new-todo').focus();
			util.store('todos-jquery', this.todos);
			util.store('list-jquery', this.list);

		},
		renderFooter: function () {
			var todoCount = this.todos.length;
			var activeTodoCount = this.getActiveTodos().length;
			var template = this.footerTemplate({
				activeTodoCount: activeTodoCount,
				activeTodoWord: util.pluralize(activeTodoCount, 'item'),
				completedTodos: todoCount - activeTodoCount,
				filter: this.filter
			});

			$('#footer').toggle(todoCount > 0).html(template);
		},
		toggleAll: function (e) {
			var isChecked = $(e.target).prop('checked');

			this.todos.forEach(function (item) {
				item.completed = isChecked;
				api.updateItem(item);
			});

			this.render();
		},
		getActiveTodos: function () {
			return this.todos.filter(function (item) {
				return !item.completed;
			});
		},
		getCompletedTodos: function () {
			return this.todos.filter(function (item) {
				return item.completed;
			});
		},
		getFilteredTodos: function () {
			if (this.filter === 'active') {
				return this.getActiveTodos();
			}

			if (this.filter === 'completed') {
				return this.getCompletedTodos();
			}
			return this.todos;
		},
		destroyCompleted: function () {
			this.getCompletedTodos().forEach(function(item) {
				api.destroyItem(item)}
			);
			this.todos = this.getActiveTodos();
			this.filter = 'all';
			this.render();
		},
		// accepts an element from inside the `.item` div and
		// returns the corresponding index in the `todos` array
		getIndexFromEl: function (el) {
			var id = $(el).closest('li').data('id');
			var todos = this.todos;
			var i = todos.length;

			while (i--) {
				if (todos[i].id === id) {
					return i;
				}
			}
		},
		create: function (e) {
			var $input = $(e.target);
			var val = $input.val().trim();

			if (e.which !== ENTER_KEY || !val) {
				return;
			}

			api.createItem(this.list[0].id, val, this.integrateItem.bind(this));
			$input.val('');

			this.render();
		},
		toggle: function (e) {
			var i = this.getIndexFromEl(e.target);
			this.todos[i].completed = !this.todos[i].completed;
			api.updateItem(this.todos[i]);
			this.render();
		},
		editingMode: function (e) {
			var $input = $(e.target).closest('li').addClass('editing').find('.edit');
			$input.val($input.val()).focus();
		},
		editKeyup: function (e) {
			if (e.which === ENTER_KEY) {
				e.target.blur();
			}

			if (e.which === ESCAPE_KEY) {
				$(e.target).data('abort', true).blur();
			}
		},
		update: function (e) {
			var el = e.target;
			var $el = $(el);
			var val = $el.val().trim();

			if (!val) {
				this.destroy(e);
				return;
			}

			if ($el.data('abort')) {
				$el.data('abort', false);
			} else {
				var todo = this.todos[this.getIndexFromEl(el)];
				todo.title = val;
				api.updateItem(todo);
			}

			this.render();
		},
		destroy: function (e) {
			var item = this.todos.splice(this.getIndexFromEl(e.target), 1)[0];
			api.destroyItem(item);
			this.render();
		},
		signin: function (e) {
			console.log(e);
		},
		notIntegratedItem: function (item) {
			return !this.todos.map(function(todo) {return todo.id;}).includes(item.id);
		},
		integrateItem: function (item) {
			this.todos.push({
				id: item.id,
				title: item.title,
				completed: item.completed || false
			});
			this.render();

		},
		integrateItems: function (data) {
			data.filter(function(item) {if (App.notIntegratedItem(item)) {return item}}).forEach(function(item) {
				App.integrateItem(item)
			});
			this.render();
		},
		integrateList: function (list) {
			// Delete all elements from list storage
			for (var i =0; i< this.list.length; i++) {
					this.list.splice(i, 1);
			}
			this.list.push({
				id: list.id,
				name: list.name
			});
			api.getItems(this.list[0].id , this.integrateItems.bind(this));
			this.render();
		}

	};

	var credentials = util.store('credentials');
	for (var i =0; i < credentials.length; i++) {
		credentials.splice(i, 1);
	}
	credentials.push({
		username: 'anonymous_user',
		password: 'anonymous_password'
	});
	util.store('credentials', credentials);
	api.getJwtToken(credentials[0], function(jwtToken) {
		console.log(jwtToken);
		api.setHeaders(jwtToken);
		App.init();
	});


});
