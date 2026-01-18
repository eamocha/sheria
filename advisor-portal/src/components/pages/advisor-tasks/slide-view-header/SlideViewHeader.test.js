import React from 'react';
import ReactDOM from 'react-dom';
import SlideViewHeader from './SlideViewHeader';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<SlideViewHeader />, div);
  ReactDOM.unmountComponentAtNode(div);
});