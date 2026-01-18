export default {
  name: "Loader",
  props: ["show"],

  setup(props) {
    return { props };
  },
  template: `<div id="v-loader-global" v-if="props.show">
    <div class="loader">
        <div>
            <div></div>
        </div>
        <div>
            <div></div>
        </div>
        <div>
            <div></div>
        </div>
        <div>
            <div></div>
        </div>
    </div>
</div>`,
};