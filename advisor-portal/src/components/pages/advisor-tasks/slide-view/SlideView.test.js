import React from 'react';
import ReactDOM from 'react-dom';
import SlideView from './SlideView';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<SlideView />, div);
  ReactDOM.unmountComponentAtNode(div);
});