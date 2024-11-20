
export class CreateDom {


    /**
     * 
     * @param {string} dom 
     * @param {object} attribut 
     * @returns {HTMLElement}
     */
    static creatDomContent(dom, attribut) {
        const domContent = document.createElement(dom);

        for (const [key, value] of Object.entries(attribut)) {
            domContent.setAttribute(key, value);
        }

        return domContent;
    }
}