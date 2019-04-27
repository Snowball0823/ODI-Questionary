;
(function (window) {

	'use strict';

	var support = {
			animations: Modernizr.cssanimations
		},
		animEndEventNames = {
			'WebkitAnimation': 'webkitAnimationEnd',
			'OAnimation': 'oAnimationEnd',
			'msAnimation': 'MSAnimationEnd',
			'animation': 'animationend'
		},
		// animation end event name
		animEndEventName = animEndEventNames[Modernizr.prefixed('animation')];

	/**
	 * extend obj function
	 */
	function extend(a, b) {
		for (var key in b) {
			if (b.hasOwnProperty(key)) {
				a[key] = b[key];
			}
		}
		return a;
	}

	/**
	 * createElement function
	 * creates an element with tag = tag, className = opt.cName, innerHTML = opt.inner and appends it to opt.appendTo
	 */
	function createElement(tag, opt) {
		var el = document.createElement(tag)
		if (opt) {
			if (opt.cName) {
				el.className = opt.cName;
			}
			if (opt.inner) {
				el.innerHTML = opt.inner;
			}
			if (opt.appendTo) {
				opt.appendTo.appendChild(el);
			}
		}
		return el;
	}

	/**
	 * FForm function
	 */
	function FForm(el, options) {
		this.el = el;
		this.options = extend({}, this.options);
		extend(this.options, options);
		this._init();
	}

	/**
	 * FForm options
	 */
	FForm.prototype.options = {
		// show progress bar
		ctrlProgress: true,
		// show navigation dots
		ctrlNavDots: true,
		// show [current field]/[total fields] status
		ctrlNavPosition: true,
		// reached the review and submit step
		onReview: function () {
			return true;
		}
	}

	/**
	 * init function
	 * initialize and cache some vars
	 */
	FForm.prototype._init = function () {
		// the form element
		this.formEl = this.el.querySelector('form');

		//console.log(this.formEl.getElementsByClassName('fs-submit'));
		//the submit button
		this.submitBt = this.formEl.querySelector('button.fs-submit');
		//console.log(this.submitBt.visibilityState);

		// list of fields
		this.fieldsList = this.formEl.querySelector('ol.fs-fields');

		// current field position
		this.current = 0;

		// all fields
		this.fields = [].slice.call(this.fieldsList.children);

		// total fields
		this.fieldsCount = this.fields.length;

		// show first field
		classie.add(this.fields[this.current], 'fs-current');

		// create/add controls
		this._addControls();

		// create/add messages
		this._addErrorMsg();

		// init events
		this._initEvents();

		//submit questionary
		this._submitQuestionary();
	}

	/**
	 * add submit function to deliver info
	 */
	FForm.prototype._submitQuestionary = function () {
		var self = this;
		//self._deliverDatas();
		//while (label) {
		this.submitBt.addEventListener('click', function () {
			//console.log(self.submitBt.visibilityState);
			self._deliverDatas();
		});
		//}
	}

	/**
	 * deliver form datas to php
	 */
	FForm.prototype._deliverDatas = function () {
		//console.log(this.submitBt.style.display);
		var radioAnswers = new Array(),
			numberAnswers = new Array(),
			index_Radio = 0,
			index_Number = 0,
			patientName,
			patientSex,
			patientID,
			returnLable = true;
		patientName = this.formEl.querySelector('input[name="q1"]').value;
		//this.formEl.querySelector('input[name="q1"]').placeholder = patientName;
		patientSex = this.formEl.querySelector('input[name="q2"]').value;
		//this.formEl.querySelector('input[name="q2"]').placeholder = patientSex;
		patientID = this.formEl.querySelector('input[name="q13"]').value;
		if (!this.formEl.querySelector('input[name="q13"]').checkValidity()) {
			returnLable = false;
		}
		(function (obj) {
			var error;
			for (var tmp in obj) {
				if (tmp == obj.length - 1) {
					if (
						(obj[tmp] > "9" || obj[tmp] < "0") &&
						(obj[tmp] != "X" && obj[tmp] != "x")
					) {
						error = 'ID Error';
						break;
					}
				} else {
					if ((obj[tmp] > "9" || obj[tmp] < "0")) {
						error = 'ID Error';
						break;
					}
				}
			}
			var message = '';
			switch (error) {
				case 'NOVAL':
					message = '请务必输入此信息';
					break;
				case 'Length Error':
					message = '请输入18位身份证号';
					break;
				case 'ID Error':
					message = '请输入正确的身份证号，中间不存在字母，最后有可能是“X”';
					break;
			};
			if (message != '') {
				window.alert(message);
				returnLable = false;
			}
		})(patientID);
		[].slice.call(this.formEl.querySelectorAll('input[type="radio"]')).forEach(function (inp) {
			if (inp.checked) {
				radioAnswers[index_Radio] = parseInt(inp.value[inp.value.length - 1]);
				index_Radio++;
			}
		});
		[].slice.call(this.formEl.querySelectorAll('input[type="number"]')).forEach(function (inp) {
			if (inp.checkValidity()) {
				numberAnswers[index_Number] = parseInt(inp.value);
				index_Number++;
			} else {
				returnLable = false;
			}
		});
		var show_all = function (obj) {
			for (var x in obj) {
				console.log(x + obj[x]);
			}
		};
		//show_all(radioAnswers);
		if (returnLable) {
			//this.formEl.querySelector('input[name="q15"]').placeholder = patientID;
			var tmp_paren_obj = document.querySelector('div[id = "fs-form-wrap"]');
			var tmp_child_obj = document.querySelector('div[class="fs-controls"]');
			tmp_paren_obj.removeChild(tmp_child_obj);
			tmp_paren_obj = document.querySelector('form[id = "myform"]');
			tmp_child_obj = document.querySelector('button[id="subButton"]');
			tmp_paren_obj.removeChild(tmp_child_obj);
			//var kindeditor = document.documentElement.innerHTML;
			//var fileName = patientName+'.html';
			//console.log(kindeditor);
			//var blob = new Blob([kindeditor], {
			//	type: "text/plain;charset=utf-8"
			//});
			//saveAs(blob, fileName);
			//show_all(kindeditor);
			//this._saveHtml2Word();
			//console.log("name:" + patientName + ",sex:" + patientSex + ",ID:" + patientID);
			//show_all(numberAnswers);
			$.ajax({
				async: true,
				cache: false,
				type: 'POST',
				url: "/php/deliver.php",
				data: {
					name: patientName,
					sex: patientSex,
					id: patientID,
					radios: JSON.stringify(radioAnswers)
				},
				traditional: true,
				beforeSend: function () {
					console.log("name:" + patientName + ",sex:" + patientSex + ",ID:" + patientID);
					show_all(radioAnswers);
				},
				success: function (result) {
					console.log(result);
					//open(location, '_self').close();
				}
			});
		} else {
			return false;
		}
	}

	/**
	 * addControls function
	 * create and insert the structure for the controls
	 */
	FForm.prototype._addControls = function () {
		// main controls wrapper
		this.ctrls = createElement('div', {
			cName: 'fs-controls',
			appendTo: this.el
		});

		// continue button (jump to next field)
		this.ctrlContinue = createElement('button', {
			cName: 'fs-continue',
			inner: ' Continue ',
			appendTo: this.ctrls
		});
		this._showCtrl(this.ctrlContinue);

		// navigation dots
		if (this.options.ctrlNavDots) {
			this.ctrlNav = createElement('nav', {
				cName: 'fs-nav-dots',
				appendTo: this.ctrls
			});
			var dots = '';
			for (var i = 0; i < this.fieldsCount; ++i) {
				dots += i === this.current ? '<button class="fs-dot-current"></button>' : '<button disabled></button>';
			}
			this.ctrlNav.innerHTML = dots;
			this._showCtrl(this.ctrlNav);
			this.ctrlNavDots = [].slice.call(this.ctrlNav.children);
		}

		// field number status
		if (this.options.ctrlNavPosition) {
			this.ctrlFldStatus = createElement('span', {
				cName: 'fs-numbers',
				appendTo: this.ctrls
			});

			// current field placeholder
			this.ctrlFldStatusCurr = createElement('span', {
				cName: 'fs-number-current',
				inner: Number(this.current + 1)
			});
			this.ctrlFldStatus.appendChild(this.ctrlFldStatusCurr);

			// total fields placeholder
			this.ctrlFldStatusTotal = createElement('span', {
				cName: 'fs-number-total',
				inner: this.fieldsCount
			});
			this.ctrlFldStatus.appendChild(this.ctrlFldStatusTotal);
			this._showCtrl(this.ctrlFldStatus);
		}

		// progress bar
		if (this.options.ctrlProgress) {
			this.ctrlProgress = createElement('div', {
				cName: 'fs-progress',
				appendTo: this.ctrls
			});
			this._showCtrl(this.ctrlProgress);
		}
	}

	/**
	 * addErrorMsg function
	 * create and insert the structure for the error message
	 */
	FForm.prototype._addErrorMsg = function () {
		// error message
		this.msgError = createElement('span', {
			cName: 'fs-message-error',
			appendTo: this.el
		});
	}

	/**
	 * init events
	 */
	FForm.prototype._initEvents = function () {
		var self = this;

		// show next field
		this.ctrlContinue.addEventListener('click', function () {
			self._nextField();
		});

		// navigation dots
		if (this.options.ctrlNavDots) {
			this.ctrlNavDots.forEach(function (dot, pos) {
				dot.addEventListener('click', function () {
					self._showField(pos);
				});
			});
		}

		// jump to next field without clicking the continue button (for fields/list items with the attribute "data-input-trigger")
		this.fields.forEach(function (fld) {
			if (fld.hasAttribute('data-input-trigger')) {
				var input = fld.querySelector('input[type="radio"]') || /*fld.querySelector( '.cs-select' ) ||*/ fld.querySelector('select'); // assuming only radio and select elements (TODO: exclude multiple selects)
				if (!input) return;

				switch (input.tagName.toLowerCase()) {
					case 'select':
						input.addEventListener('change', function () {
							self._nextField();
						});
						break;

					case 'input':
						[].slice.call(fld.querySelectorAll('input[type="radio"]')).forEach(function (inp) {
							inp.addEventListener('change', function (ev) {
								self._nextField();
							});
						});
						break;

						/*
						// for our custom select we would do something like:
						case 'div' : 
							[].slice.call( fld.querySelectorAll( 'ul > li' ) ).forEach( function( inp ) {
								inp.addEventListener( 'click', function(ev) { self._nextField(); } );
							} ); 
							break;
						*/
				}
			}
		});
		// keyboard navigation events - jump to next field when pressing enter
		document.addEventListener('keydown', function (ev) {
			if (!self.isLastStep && ev.target.tagName.toLowerCase() !== 'textarea') {
				var keyCode = ev.keyCode || ev.which;
				if (keyCode === 13) {
					ev.preventDefault();
					self._nextField();
				}
			} //else if (this.submitBt.display != )
		});
	}

	/**
	 * nextField function
	 * jumps to the next field
	 */
	FForm.prototype._nextField = function (backto) {
		/*if (this.isLastStep ){
			window.close();
			return true;
		}*/
		if (this.isLastStep || !this._validade() || this.isAnimating) {
			//this.isLastStep ||
			//window.close();
			return false;
		}
		this.isAnimating = true;

		// check if on last step
		this.isLastStep = this.current === this.fieldsCount - 1 && backto === undefined ? true : false;

		// clear any previous error messages
		this._clearError();

		// current field
		var currentFld = this.fields[this.current];

		// save the navigation direction
		this.navdir = backto !== undefined ? backto < this.current ? 'prev' : 'next' : 'next';

		// update current field
		this.current = backto !== undefined ? backto : this.current + 1;

		if (backto === undefined) {
			// update progress bar (unless we navigate backwards)
			this._progress();

			// save farthest position so far
			this.farthest = this.current;
		}

		// add class "fs-display-next" or "fs-display-prev" to the list of fields
		classie.add(this.fieldsList, 'fs-display-' + this.navdir);

		// remove class "fs-current" from current field and add it to the next one
		// also add class "fs-show" to the next field and the class "fs-hide" to the current one
		classie.remove(currentFld, 'fs-current');
		classie.add(currentFld, 'fs-hide');

		if (!this.isLastStep) {
			// update nav
			this._updateNav();

			// change the current field number/status
			this._updateFieldNumber();

			var nextField = this.fields[this.current];
			classie.add(nextField, 'fs-current');
			classie.add(nextField, 'fs-show');
		}

		// after animation ends remove added classes from fields
		var self = this,
			onEndAnimationFn = function (ev) {
				if (support.animations) {
					this.removeEventListener(animEndEventName, onEndAnimationFn);
				}

				classie.remove(self.fieldsList, 'fs-display-' + self.navdir);
				classie.remove(currentFld, 'fs-hide');

				if (self.isLastStep) {
					// show the complete form and hide the controls
					self._hideCtrl(self.ctrlNav);
					self._hideCtrl(self.ctrlProgress);
					self._hideCtrl(self.ctrlContinue);
					self._hideCtrl(self.ctrlFldStatus);
					// replace class fs-form-full with fs-form-overview
					classie.remove(self.formEl, 'fs-form-full');
					classie.add(self.formEl, 'fs-form-overview');
					classie.add(self.formEl, 'fs-show');
					// callback
					self.options.onReview();
				} else {
					classie.remove(nextField, 'fs-show');
					if (self.options.ctrlNavPosition) {
						self.ctrlFldStatusCurr.innerHTML = self.ctrlFldStatusNew.innerHTML;
						self.ctrlFldStatus.removeChild(self.ctrlFldStatusNew);
						classie.remove(self.ctrlFldStatus, 'fs-show-' + self.navdir);
					}
				}
				self.isAnimating = false;
			};

		if (support.animations) {
			if (this.navdir === 'next') {
				if (this.isLastStep) {
					currentFld.querySelector('.fs-anim-upper').addEventListener(animEndEventName, onEndAnimationFn);
				} else {
					nextField.querySelector('.fs-anim-lower').addEventListener(animEndEventName, onEndAnimationFn);
				}
			} else {
				nextField.querySelector('.fs-anim-upper').addEventListener(animEndEventName, onEndAnimationFn);
			}
		} else {
			onEndAnimationFn();
		}
	}

	/**
	 * showField function
	 * jumps to the field at position pos
	 */
	FForm.prototype._showField = function (pos) {
		if (pos === this.current || pos < 0 || pos > this.fieldsCount - 1) {
			return false;
		}
		this._nextField(pos);
	}

	/**
	 * updateFieldNumber function
	 * changes the current field number
	 */
	FForm.prototype._updateFieldNumber = function () {
		if (this.options.ctrlNavPosition) {
			// first, create next field number placeholder
			this.ctrlFldStatusNew = document.createElement('span');
			this.ctrlFldStatusNew.className = 'fs-number-new';
			this.ctrlFldStatusNew.innerHTML = Number(this.current + 1);

			// insert it in the DOM
			this.ctrlFldStatus.appendChild(this.ctrlFldStatusNew);

			// add class "fs-show-next" or "fs-show-prev" depending on the navigation direction
			var self = this;
			setTimeout(function () {
				classie.add(self.ctrlFldStatus, self.navdir === 'next' ? 'fs-show-next' : 'fs-show-prev');
			}, 25);
		}
	}

	/**
	 * progress function
	 * updates the progress bar by setting its width
	 */
	FForm.prototype._progress = function () {
		if (this.options.ctrlProgress) {
			this.ctrlProgress.style.width = this.current * (100 / this.fieldsCount) + '%';
		}
	}

	/**
	 * updateNav function
	 * updates the navigation dots
	 */
	FForm.prototype._updateNav = function () {
		if (this.options.ctrlNavDots) {
			classie.remove(this.ctrlNav.querySelector('button.fs-dot-current'), 'fs-dot-current');
			classie.add(this.ctrlNavDots[this.current], 'fs-dot-current');
			this.ctrlNavDots[this.current].disabled = false;
		}
	}

	/**
	 * showCtrl function
	 * shows a control
	 */
	FForm.prototype._showCtrl = function (ctrl) {
		classie.add(ctrl, 'fs-show');
	}

	/**
	 * hideCtrl function
	 * hides a control
	 */
	FForm.prototype._hideCtrl = function (ctrl) {
		classie.remove(ctrl, 'fs-show');
	}

	// TODO: this is a very basic validation function. Only checks for required fields..
	FForm.prototype._validade = function () {
		var fld = this.fields[this.current],
			input = fld.querySelector('input') || fld.querySelector('textarea[required]') || fld.querySelector('select[required]'),
			error,
			label = false,
			sexs = ["男", "女"];

		if (!input) return true;

		switch (input.tagName.toLowerCase()) {
			case 'input':
				if (input.type === 'radio' || input.type === 'checkbox') {
					var checked = 0;
					//console.log(fld.querySelectorAll('input[type="' + input.type + '"]'));
					[].slice.call(fld.querySelectorAll('input[type="' + input.type + '"]')).forEach(function (inp) {
						if (inp.checked) {
							++checked;
						}
					});
					if (checked == 0) {
						error = 'NOVAL';
					}
					//window.alert(checked);
				} else if (input.type === 'text') {
					if (input.id === 'q2') {
						if (input.value === '') {
							error = 'NOVAL';
							break;
						}
						for (var x in sexs) {
							if (input.value === sexs[x]) {
								label = true;
								break;
							}
						}
						if (!label) {
							error = 'SEX Error';
							break;
						}
					} else if (input.id === 'q13') {
						if (input.value === '') {
							error = 'NOVAL';
							break;
						}
						if (input.value.length != 18) {
							error = 'Length Error';
							break;
						}
						for (var tmp in input.value) {
							//console.log(tmp, input.value[tmp]);
							if (tmp == input.value.length - 1) {
								if (
									(input.value[tmp] > "9" || input.value[tmp] < "0") &&
									(input.value[tmp] != "X" && input.value[tmp] != "x")
								) {
									error = 'ID Error';
									//console.log('finalError');
									break;
								}
							} else {
								if ((input.value[tmp] > "9" || input.value[tmp] < "0")) {
									error = 'ID Error';
									break;
								}
							}
						}
					} else {
						if (input.value === '') {
							error = 'NOVAL';
							break;
						}
					}
				}
				break;

			case 'select':
				// assuming here '' or '-1' only
				if (input.value === '' || input.value === '-1') {
					error = 'NOVAL';
				}
				break;

			case 'textarea':
				if (input.value === '') {
					error = 'NOVAL';
				}
				break;
		}

		if (error != undefined) {
			this._showError(error);
			return false;
		}

		return true;
	}

	// TODO
	FForm.prototype._showError = function (err) {
		var message = '';
		switch (err) {
			case 'NOVAL':
				message = '请务必输入此信息';
				break;
			case 'INVALIDEMAIL':
				message = '请输入有用的邮箱';
				break;
			case 'SEX Error':
				message = '请输入正确的性别（男或女）';
				break;
			case 'Length Error':
				message = '请输入18位身份证号';
				break;
			case 'ID Error':
				message = '请输入正确的身份证号，中间不存在字母，最后有可能是“X”';
				break;
				// ...
		};
		this.msgError.innerHTML = message;
		this._showCtrl(this.msgError);
	}

	// clears/hides the current error message
	FForm.prototype._clearError = function () {
		this._hideCtrl(this.msgError);
	}

	// add to global namespace
	window.FForm = FForm;
})(window);