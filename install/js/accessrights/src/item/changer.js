import {EventEmitter} from "main.core.events";
import {Dom, Tag} from "main.core";
import Base from "./base";
import ColumnItemOptions from "../columnitem";

export default class Changer extends Base {
    constructor(options: ColumnItemOptions) {
        super(options);

        this.isModify = false;
    }

    getChanger(): HTMLElement {
        if (!this.changer) {
            this.changer = Tag.render`<a class='ui-access-rights-column-item-changer'></a>`
        }

        return this.changer;
    }

    bindEvents(): void {
        EventEmitter.subscribe('BX.KosmosAccess.AccessRights:reset', this.offChanger.bind(this));
        EventEmitter.subscribe('BX.KosmosAccess.AccessRights:refresh', this.refreshStatus.bind(this));
    }

    refreshStatus(): void {
        Dom.removeClass(this.getChanger(), 'ui-access-rights-column-item-changer-on')
    }

    offChanger(): void {
        if (this.isModify) {
            setTimeout(() => {
                this.refreshStatus();
            });
        }
    }

    adjustChanger(): void {
        this.isModify = !this.isModify;

        Dom.toggleClass(this.getChanger(), 'ui-access-rights-column-item-changer-on');
    }
}
