export default {
    name: 'ToolTip',
    props: ['content', 'position'],

    setup(props) {
        return { content:props.content, position:props.position };
    },
    template: `
    <div class="vue-tooltip" :class="position">
        <slot></slot>
        <span class="tooltip-text">{{content}}</span>
    </div>`,
};