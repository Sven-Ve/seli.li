import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static values = { url: String };
  static targets = ["button"]

  async set(event) {
    const category = event.target.getAttribute("data-category-id");

    let url = this.urlValue;
    if (category !== "0") {
      url = this.urlValue + "&catId=" + category;
    }
    
    this.dispatch('success', {url: url});
    this.setCatStats(category);
    window.history.pushState('', '',
      url.replace('?ajax=1&', '?').replace('?ajax=1','')
    );
  }

  /**
   * set the selected button in the category selector
   * @param {*} category
   */
  setCatStats(category) {
    this.buttonTargets.forEach((element) => {
      if (element.getAttribute("data-category-id") === category) {
        element.classList.add('selected')
      } else {
        element.classList.remove('selected');
      }
    })
  }

}
