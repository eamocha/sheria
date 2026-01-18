import React from 'react';
import ReactDOM from 'react-dom';
import SlideViewBody from './SlideViewBody';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<SlideViewBody />, div);
  ReactDOM.unmountComponentAtNode(div);
});