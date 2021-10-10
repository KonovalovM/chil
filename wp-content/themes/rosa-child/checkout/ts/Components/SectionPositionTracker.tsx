import React from 'react';
import debounce from 'lodash/debounce';
import min from 'lodash/min';
import map from 'lodash/map';
import isArray from 'lodash/isArray';
import findIndex from 'lodash/findIndex';

interface SectionPositionTrackerProps<SectionData> {
  sectionsData: SectionData[];
  currentSection: SectionData;
  onSectionChange: (sectionData: SectionData) => void;
}

export class SectionPositionTracker<SectionData extends { id: string; name: string }> extends React.Component<SectionPositionTrackerProps<SectionData>, {}> {
  sectionRefs: Array<React.RefObject<HTMLDivElement>> = [];
  scrolling = false;

  constructor(props: SectionPositionTrackerProps<SectionData>) {
    super(props);

    this.handleScroll = debounce(this.handleScroll.bind(this), 100);
    this.scrollToSection = this.scrollToSection;
  }

  componentDidMount() {
    document.addEventListener('scroll', this.handleScroll);
  }

  componentWillUnmount() {
    document.removeEventListener('scroll', this.handleScroll);
  }

  getRef(sectionIndex: number) {
    const sectionRefs = this.sectionRefs;
    while (sectionIndex >= sectionRefs.length) {
      sectionRefs.push(React.createRef<HTMLDivElement>());
    }
    return sectionRefs[sectionIndex];
  }

  getRefPosition(ref: React.RefObject<HTMLDivElement>) {
    const element = ref.current;
    if (!element) {
      return NaN;
    }

    const top = element.getBoundingClientRect().top;
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    return Math.abs(top + scrollTop);
  }

  getRefPositions(): number[] {
    const sectionRefs = this.sectionRefs;
    const positions = map(sectionRefs, ref => this.getRefPosition(ref));

    //normalizing positions
    const minPos = min(positions) || 0;
    return map(positions, pos => pos - minPos);
  }

  getRefHeights(): number[] {
    const sectionRefs = this.sectionRefs;
    const heights = map(sectionRefs, ref => (ref.current ? ref.current.offsetHeight : NaN));

    return heights;
  }

  scrollToSection(section: SectionData) {
    const sectionPositions = this.getRefPositions();
    const sectionIndex = findIndex(this.props.sectionsData, ['id', section.id]);
    const position = sectionPositions[sectionIndex];

    if (Number.isNaN(position)) {
      return;
    }

    this.scrolling = true;
    window.scrollTo({
      top: position,
      left: 0,
      behavior: 'smooth',
    });
  }

  handleScroll() {
    const { currentSection, sectionsData, onSectionChange } = this.props;
    const currentSectionIndex = findIndex(sectionsData, ['id', currentSection.id]);

    const startingPositions = this.getRefPositions();
    const refHeights = this.getRefHeights();

    const endingPositions = map(startingPositions, (pos, index) => pos + refHeights[index] / 2);

    const pageYOffset = window.pageYOffset;

    const sectionsInRange = map(startingPositions, (pos, index) => {
      const tolerance = 20;
      return pageYOffset + tolerance > pos && pageYOffset < endingPositions[index];
    });

    const newIndex = findIndex(sectionsInRange);

    if (newIndex !== -1 && newIndex !== currentSectionIndex) {
      onSectionChange(sectionsData[newIndex]);
    }
  }

  render() {
    if (isArray(this.props.children)) {
      return (
        <div className="section-position-tracker">
          {map(this.props.children, (child, index) => (
            <div key={index} ref={this.getRef(index)}>
              {child}
            </div>
          ))}
        </div>
      );
    }
    return null;
  }
}
