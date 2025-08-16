import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { createMountOptions } from '../utils/testHelpers.js';

// 簡單的測試組件
const TestComponent = {
  template: `
    <div class="test-component">
      <h1>{{ title }}</h1>
      <button @click="increment">Count: {{ count }}</button>
    </div>
  `,
  data() {
    return {
      title: 'Vue 測試環境',
      count: 0
    };
  },
  methods: {
    increment() {
      this.count++;
    }
  }
};

describe('Vue 測試環境驗證', () => {
  it('應該能夠掛載組件', () => {
    const wrapper = mount(TestComponent, createMountOptions());
    
    expect(wrapper.exists()).toBe(true);
    expect(wrapper.find('h1').text()).toBe('Vue 測試環境');
  });

  it('應該能夠與組件互動', async () => {
    const wrapper = mount(TestComponent, createMountOptions());
    const button = wrapper.find('button');
    
    expect(button.text()).toBe('Count: 0');
    
    await button.trigger('click');
    expect(button.text()).toBe('Count: 1');
    
    await button.trigger('click');
    expect(button.text()).toBe('Count: 2');
  });

  it('應該能夠測試組件 props', () => {
    const PropsComponent = {
      template: '<div>{{ message }}</div>',
      props: ['message']
    };
    
    const wrapper = mount(PropsComponent, {
      props: { message: '測試訊息' },
      ...createMountOptions()
    });
    
    expect(wrapper.text()).toBe('測試訊息');
  });
});