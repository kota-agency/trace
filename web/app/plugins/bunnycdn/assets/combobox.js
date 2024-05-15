/**
 * MIT License
 *
 * Copyright (c) 2023 Rafael Kassner
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
import GithubCombobox from '@github/combobox-nav';
export default class Combobox {
    constructor(inputSelector, listSelector, selectedSelector, options = {}) {
        this.options = {
            disableSelected: false,
            replaceString: '{{value}}',
            preCreateTransform: (content) => content,
        };
        Object.assign(this.options, options);
        this.input = document.querySelector(inputSelector);
        this.list = document.querySelector(listSelector);
        this.selected = document.querySelector(selectedSelector);
        this.comboboxController = new GithubCombobox(this.input, this.list);
        this.bindEvents();
    }
    bindEvents() {
        this.input.addEventListener('input', () => {
            this.filterList();
            this.toggleList();
        });
        this.input.addEventListener('focus', () => this.toggleList());
        this.input.addEventListener('blur', () => {
            setTimeout(() => {
                this.list.setAttribute('hidden', 'hidden');
                this.comboboxController.clearSelection();
                this.comboboxController.stop();
            }, 50);
        });
        this.list.addEventListener('combobox-commit', (event) => {
            var _a, _b;
            const target = event.target;
            let content = target.textContent || '';
            if (((_a = target.attributes.getNamedItem('data-create')) === null || _a === void 0 ? void 0 : _a.value) == 'true') {
                content = this.input.value;
            }
            content = this.options.preCreateTransform(content);
            const template = (_b = this.selected.querySelector('template')) === null || _b === void 0 ? void 0 : _b.cloneNode(true);
            const newHtml = this.options.replaceString == null ? template.innerHTML : replaceAll(template.innerHTML, this.options.replaceString, escapeHtml(content));
            this.selected.insertAdjacentHTML('beforeend', newHtml.trim());
            if (this.options.disableSelected != true) {
                target.setAttribute('aria-disabled', 'true');
            }
            this.list.setAttribute('hidden', 'hidden');
            this.input.value = '';
            this.comboboxController.clearSelection();
            this.comboboxController.stop();
        });
        this.list.addEventListener('mousedown', (event) => {
            const target = event.target;
            if (target.matches('[role=option]')) {
                const ev = new CustomEvent('combobox-commit', { bubbles: true, detail: { event } });
                target.dispatchEvent(ev);
            }
        });
        this.list.addEventListener('mouseover', (event) => {
            const target = event.target;
            if (target.matches('li')) {
                this.list.querySelectorAll('li[aria-selected=true]').forEach((value) => {
                    value.removeAttribute('aria-selected');
                });
                target.setAttribute('aria-selected', 'true');
            }
        });
        this.selected.addEventListener('click', (event) => {
            var _a;
            const target = event.target;
            if (target.matches('[role=remove]') || target.closest('[role=remove]')) {
                (_a = target.closest('[role=listitem]')) === null || _a === void 0 ? void 0 : _a.remove();
            }
        });
    }
    filterList() {
        this.list.querySelectorAll('[data-create=true]').forEach(item => item.remove());
        for (let item of this.list.children) {
            const el = item;
            if (el.innerText.toLowerCase().includes(this.input.value.toLowerCase())) {
                item.removeAttribute('hidden');
            }
            else {
                item.setAttribute('hidden', 'hidden');
            }
        }
        if (this.input.value.length == 0) {
            return;
        }
        const createTemplate = this.list.querySelector('template[data-role=create]');
        if (createTemplate != undefined) {
            const template = createTemplate.cloneNode(true);
            const newHtml = this.options.replaceString == null ? template.innerHTML : replaceAll(template.innerHTML, this.options.replaceString, escapeHtml(this.input.value));
            this.list.insertAdjacentHTML('beforeend', newHtml.trim());
        }
    }
    toggleList() {
        const hidden = this.input.value.length === 0;
        if (hidden) {
            this.list.setAttribute('hidden', 'hidden');
            this.comboboxController.stop();
        }
        else {
            this.list.removeAttribute('hidden');
            this.comboboxController.start();
        }
    }
}
const escapeHtml = (text) => {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
};
const regExpEscape = (text) => {
    return text.replace(/[$-\/?[-^{|}]/g, '\\$&');
};
const replaceAll = (text, search, replacement) => {
    return text.replace(new RegExp(regExpEscape(search), 'g'), replacement);
};
